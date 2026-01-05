<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mentor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'mentor') {
            redirect('auth/login');
        }
        $this->load->model(['M_Admin', 'M_Absensi', 'M_Auth', 'M_Master']);
    }

    private function render_view($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/admin_template', $data);
    }

    public function index() {
		$divisi_id = $this->session->userdata('divisi_id');
		$data['title'] = $this->session->userdata('nama_lengkap') . ' | ' . strtoupper($this->session->userdata('role'));

		// Ambil semua summary kebutuhan
		$all_summary = $this->M_Master->get_kebutuhan_divisi_summary();
		$data['my_summary'] = null;
		foreach($all_summary as $s) {
			if($s->id == $divisi_id) {
				$data['my_summary'] = $s;
				break;
			}
		}

		// Get Nama Divisi secara dinamis
		$divisi = $this->db->get_where('master_divisi', ['id' => $divisi_id])->row();
		$data['nama_divisi'] = $divisi ? $divisi->nama_divisi : 'N/A';
		
		// Filter Statistik: Gunakan role_id = 3 (Peserta)
		$data['total_magang'] = $this->db->where([
			'divisi_id' => $divisi_id, 
			'role_id'   => 3 
		])->count_all_results('users');
		
		$today = date('Y-m-d');
		$data['hadir_today'] = $this->db->join('users', 'users.id = absensi.user_id')
										->where([
											'users.divisi_id' => $divisi_id, 
											'users.role_id'   => 3, 
											'absensi.tanggal' => $today
										])
										->count_all_results('absensi');

		// List Peserta: Filter role_id = 3
		$data['peserta'] = $this->db->select('pendaftar.*, users.email, users.id as user_id')
									->from('pendaftar')
									->join('users', 'users.id = pendaftar.user_id')
									->where([
										'users.divisi_id' => $divisi_id,
										'users.role_id'   => 3
									])
									->get()->result();

		$this->render_view('mentor/dashboard', $data);
	}

    public function monitoring_absensi() {
        $divisi_id = $this->session->userdata('divisi_id');
        $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
        
        $data['title'] = 'Monitoring Absensi Divisi';
        $data['tanggal'] = $tanggal;
        
        // Query Absensi terfilter Divisi & role_id = 3
        $data['absensi'] = $this->db->select('u.nama_lengkap, a.*, u.id as user_id')
                                    ->from('users u')
                                    ->join('absensi a', "u.id = a.user_id AND a.tanggal = '$tanggal'", 'left')
                                    ->where([
                                        'u.divisi_id' => $divisi_id, 
                                        'u.role_id'   => 3
                                    ])
                                    ->get()->result();

        $this->render_view('mentor/monitoring_absensi', $data);
    }

    public function rekap_absensi($user_id) {
        require_once FCPATH . 'vendor/autoload.php';
        
        // Security Check: Pastikan mentor hanya bisa akses peserta divisinya sendiri
        $u_divisi = $this->session->userdata('divisi_id');
        $peserta = $this->M_Auth->get_user_by_id($user_id);
        
        if(!$peserta || $peserta->divisi_id != $u_divisi) {
            show_404();
        }

        $data['peserta'] = $peserta;
        $data['detail']  = $this->M_Admin->get_pendaftar_by_user($user_id);
        $data['absensi'] = $this->M_Absensi->get_absensi_by_user($user_id);

        $dompdf = new \Dompdf\Dompdf();
        $html = $this->load->view('laporan/pdf_absensi', $data, TRUE);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Rekap_Absensi_{$peserta->nama_lengkap}.pdf", ["Attachment" => 0]);
    }

	public function request_magang() {
		$div_id = $this->session->userdata('divisi_id');
		$divisi = $this->db->get_where('master_divisi', ['id' => $div_id])->row();
		
		$data['title'] = 'Permintaan Anak Magang';
		$data['nama_divisi'] = $divisi ? $divisi->nama_divisi : 'N/A';
		$data['requests'] = $this->M_Master->get_requests_by_divisi($div_id);
		
		$this->render_view('mentor/request_magang', $data);
	}

	public function add_request() {
		$data = [
			'divisi_id' => $this->session->userdata('divisi_id'),
			'jumlah'    => $this->input->post('jumlah'),
			'keterangan'=> $this->input->post('keterangan')
		];
		$this->M_Master->insert_request($data);
		$this->session->set_flashdata('success', 'Permintaan berhasil dikirim ke HC');
		redirect('mentor/request_magang');
	}

	public function delete_request($id) {
		$this->M_Master->delete_request($id);
		$this->session->set_flashdata('success', 'Permintaan berhasil dihapus');
		redirect('mentor/request_magang');
	}
}
