<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
		parent::__construct();
		$role = $this->session->userdata('role');
		if (!$this->session->userdata('logged_in') || !in_array($role, ['admin', 'mentor'])) {
			redirect('auth/login');
		}

        $this->load->model('M_Admin');
		$this->load->model('M_Master'); 
		$this->load->model('M_Absensi');
		$this->load->model('M_Auth');
	}

    private function render_view($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/admin_template', $data);
    }

    public function index()
	{
		$data['title'] = $this->session->userdata('nama_lengkap') . ' | ' . strtoupper($this->session->userdata('role'));
		$today = date('Y-m-d');

		$stats = $this->M_Admin->get_stats_pendaftaran();
		$data['total_daftar'] = $stats['total'];
		$data['pending']      = $stats['pending'];
		$data['aktif']        = $stats['aktif'];
		$data['selesai']      = $stats['selesai'];

		$absen_stats = $this->M_Admin->get_stats_absensi_today($today);
		$data['hadir']      = $absen_stats['hadir'];
		$data['telat']      = $absen_stats['telat'];
		$data['absen_izin'] = $absen_stats['izin'];
		$data['belum_absen'] = max(0, $data['aktif'] - ($data['hadir'] + $data['telat'] + $data['absen_izin']));
		
		$data['divisi'] = $this->M_Master->get_all_divisi();
		$data['lokasi'] = $this->M_Master->get_all_lokasi();
		$data['kebutuhan_divisi'] = $this->M_Master->get_kebutuhan_divisi_summary();

		$data['pendaftar'] = $this->M_Admin->get_all_pendaftar();

		$this->render_view('admin/dashboard_lte', $data);
	}

    public function berkas($id)
    {
        $data['pendaftar'] = $this->M_Admin->get_pendaftar_by_id($id);
        if (!$data['pendaftar']) show_404();
		$data['kebutuhan_divisi'] = $this->M_Master->get_kebutuhan_divisi_summary();
        $data['dokumen'] = $this->M_Admin->get_dokumen_by_pendaftar($id);
        $data['akun'] = null;

        if ($data['pendaftar']->user_id) {
            $data['akun'] = $this->M_Auth->get_user_by_id($data['pendaftar']->user_id);
        }

		$data['divisi_list'] = $this->M_Master->get_all_divisi();
    	$data['lokasi_list'] = $this->M_Master->get_all_lokasi();
        $data['title'] = 'Detail Berkas Peserta';
        $this->render_view('admin/detail_berkas', $data);
    }

    public function verifikasi($id, $status) {
    if (!in_array($status, ['diterima', 'ditolak'])) redirect('admin');
    
    // 1. Ambil data pendaftar
    $pendaftar = $this->M_Admin->get_pendaftar_by_id($id);
    if (!$pendaftar) show_404();
    $pendaftar->status = $status; 
    require_once FCPATH . 'vendor/autoload.php'; 
    $update_data = ['status' => $status];

    // --- CASE TERIMA ---
    if ($status === 'diterima') {
		$divisi_final = $this->input->post('divisi_id_final');
		$lokasi_final = $this->input->post('lokasi_id_final');
		
		// Ambil info nama divisi & lokasi untuk isi surat
		$d_info = $this->db->get_where('master_divisi', ['id' => $divisi_final])->row();
		$l_info = $this->db->get_where('master_lokasi', ['id' => $lokasi_final])->row();        
		
		$password_plain = '123456';
		$data_user = [
			'email'        => $pendaftar->email,
			'password'     => password_hash($password_plain, PASSWORD_DEFAULT),
			'role'         => 'peserta',
			'role_id'      => 3,
			'divisi_id'    => $divisi_final,
			'lokasi_id'    => $lokasi_final,
			'nama_lengkap' => $pendaftar->nama
		];
		
		$filename = 'Draft_Terima_' . time() . '.pdf';
		$html = $this->load->view('laporan/surat_balasan', [
			'pendaftar'    => $pendaftar,
			'nama_divisi'  => $d_info ? $d_info->nama_divisi : '-',
			'nama_lokasi'  => $l_info ? $l_info->nama_lokasi : '-'
		], TRUE);
		
		require_once FCPATH . 'vendor/autoload.php';
		$dompdf = new \Dompdf\Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->render();
		
		file_put_contents(FCPATH . 'assets/uploads/surat_balasan/' . $filename, $dompdf->output());

		$update_data['file_draft_balasan'] = $filename;
		$update_data['status'] = 'diterima';
		$update_data['divisi_id'] = $divisi_final;
		
		$this->M_Admin->proses_verifikasi_diterima($id, $update_data, $data_user);
		$this->session->set_flashdata('success', 'Peserta diterima & Akun telah dibuat.');
	}
    // --- CASE TOLAK ---
    else {
        $filename = 'Draft_Tolak_' . time() . '.pdf';
        $html = $this->load->view('laporan/surat_balasan', ['pendaftar' => $pendaftar], TRUE);
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        file_put_contents(FCPATH . 'assets/uploads/surat_balasan/' . $filename, $dompdf->output());

        $update_data['file_draft_balasan'] = $filename;
        
        // Update DB
        $this->db->update('pendaftar', $update_data, ['id' => $id]);
        $this->session->set_flashdata('success', 'Peserta ditolak.');
    }
    
    redirect('admin/berkas/'.$id);
}

public function upload_file_final() {
    $id = $this->input->post('id');
    $notif_method = $this->input->post('notif_method');
    $pendaftar = $this->M_Admin->get_pendaftar_by_id($id);

	$user_detail = $this->db->select('u.*, d.nama_divisi, l.nama_lokasi')
                            ->from('users u')
                            ->join('master_divisi d', 'd.id = u.divisi_id', 'left')
                            ->join('master_lokasi l', 'l.id = u.lokasi_id', 'left')
                            ->where('u.id', $pendaftar->user_id)
                            ->get()->row();

    $config['upload_path']   = './assets/uploads/surat_balasan/';
    $config['allowed_types'] = 'pdf';
    $config['file_name']     = 'Final_' . $id . '_' . time();

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('file_upload')) {
        $file_name = $this->upload->data('file_name');
        $file_url  = base_url('assets/uploads/surat_balasan/' . $file_name);
        $file_path = FCPATH . 'assets/uploads/surat_balasan/' . $file_name;

        $this->db->update('pendaftar', ['file_surat_balasan' => $file_name], ['id' => $id]);

        // PESAN DINAMIS
        if ($pendaftar->status == 'diterima') {
            $divisi_txt = $user_detail ? $user_detail->nama_divisi : 'N/A';
            $lokasi_txt = $user_detail ? $user_detail->nama_lokasi : 'N/A';

            $msg_wa = "Halo *{$pendaftar->nama}*,\n\nSelamat! Anda *DINYATAKAN DITERIMA* magang di KIT.\n\n*DETAIL PENEMPATAN:*\nDivisi: *{$divisi_txt}*\nLokasi: *{$lokasi_txt}*\n\nLogin Sistem:\nEmail: *{$pendaftar->email}*\nPass: *123456*\n\nSurat resmi terlampir.";
            
            $msg_email = "<h3>Selamat, {$pendaftar->nama}!</h3>
                          <p>Anda diterima magang di <strong>{$divisi_txt}</strong> yang berlokasi di <strong>{$lokasi_txt}</strong>.</p>
                          <p>Akun login:<br>Email: {$pendaftar->email}<br>Password: 123456</p>";
        } else {
            $msg_wa = "Halo *{$pendaftar->nama}*,\n\nMohon maaf, Anda *BELUM DAPAT DITERIMA* magang di Krakatau Information Technology (KIT) periode ini.\n\nTetap semangat! Surat resmi terlampir.";
            $msg_email = "<h3>Halo, {$pendaftar->nama}</h3><p>Mohon maaf, pengajuan magang Anda <strong>BELUM DAPAT DITERIMA</strong>.</p>";
        }

        // TRIGGER NOTIFIKASI
        if ($notif_method == 'wa') {
            $this->wa_client->send_message($pendaftar->no_hp, $msg_wa, $file_url);
        } else {
            $this->load->library('email');
			$this->load->config('email');
			$from_mail = $this->config->item('from_email');
			$from_name = $this->config->item('from_name');
            $this->email->from('fatihmaulana8@gmail.com', 'Krakatau Information Technology (KIT)');
            $this->email->to($pendaftar->email);
            $this->email->subject('Informasi Status Magang Krakatau Information Technology (KIT)');
            $this->email->message($msg_email);
            $this->email->attach($file_path);
            $this->email->send();
        }
        $this->session->set_flashdata('success', 'File Berhasil di Upload & Notifikasi Terkirim!');
    }
    redirect('admin/berkas/'.$id);
}

public function cetak_sertifikat($pendaftar_id) {
    require_once FCPATH . 'vendor/autoload.php';
    $pendaftar = $this->M_Admin->get_pendaftar_by_id($pendaftar_id);
    if (!$pendaftar) show_404();

    $data['pendaftar'] = $pendaftar;
    $html = $this->load->view('laporan/pdf_sertifikat', $data, TRUE);

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape'); // Sertifikat biasanya landscape
    $dompdf->render();
    $dompdf->stream("Draft_Sertifikat_{$pendaftar->nama}.pdf", ["Attachment" => 0]);
}

// // --- Method Baru: Handler Upload dari Admin (Surat Balasan & Sertifikat) ---
// public function upload_file_final() {
//     $id = $this->input->post('id');
//     $tipe = $this->input->post('tipe'); // 'surat' atau 'sertifikat'

//     if (empty($_FILES['file_upload']['name'])) {
//         $this->session->set_flashdata('error', 'File belum dipilih.');
//         redirect($_SERVER['HTTP_REFERER']);
//     }

//     // Config Upload
//     $folder = ($tipe == 'surat') ? 'surat_balasan' : 'sertifikat';
//     $config['upload_path']   = './assets/uploads/' . $folder . '/';
//     $config['allowed_types'] = 'pdf';
//     $config['max_size']      = 2048;
//     $config['file_name']     = $tipe . '_final_' . $id . '_' . time();

//     if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

//     $this->load->library('upload', $config);

//     if ($this->upload->do_upload('file_upload')) {
//         $file_data = $this->upload->data();
//         $file_name = $file_data['file_name'];

//         if ($tipe == 'surat') {
//             $this->db->update('pendaftar', ['file_surat_balasan' => $file_name], ['id' => $id]);
//             $msg = "Surat Balasan E-Sign berhasil diupload.";
//         } else {
//             $this->db->update('pendaftar', ['file_sertifikat' => $file_name], ['id' => $id]);
//             $msg = "Sertifikat Final berhasil diupload.";
//         }

//         $this->session->set_flashdata('success', $msg);
//     } else {
//         $this->session->set_flashdata('error', $this->upload->display_errors());
//     }

//     redirect($_SERVER['HTTP_REFERER']);
// }

    public function set_selesai($id)
    {
        $this->M_Admin->update_status_pendaftar($id, 'selesai');
        $this->session->set_flashdata('success', 'Status magang selesai');
        redirect('admin');
    }

    public function broadcast()
    {
        $data['title'] = 'Broadcast WhatsApp';
        $this->render_view('admin/broadcast', $data);
    }

    public function send_broadcast()
    {
        $no = $this->input->post('no_tujuan');
        $pesan = $this->input->post('pesan');

        if ($no && $pesan) {
            $result = $this->wa_client->send_message($no, $pesan);
            $this->session->set_flashdata(
                $result['status'] ? 'success' : 'error',
                $result['status'] ? 'Pesan terkirim' : $result['message']
            );
        }
        redirect('admin/broadcast');
    }

    public function rekap_absensi($user_id)
    {
        require_once FCPATH . 'vendor/autoload.php';

        // Memanggil fungsi yang baru saja ditambahkan di M_Auth
        $data['peserta'] = $this->M_Auth->get_user_by_id($user_id);
        if (!$data['peserta']) show_404();

        $data['detail'] = $this->M_Admin->get_pendaftar_by_user($user_id);
        $data['absensi'] = $this->M_Absensi->get_absensi_by_user($user_id);

        $dompdf = new \Dompdf\Dompdf();
        $html = $this->load->view('laporan/pdf_absensi', $data, TRUE);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Rekap_Absensi_{$data['peserta']->nama_lengkap}.pdf", ["Attachment" => 0]);
    }

	public function master_kampus() {
        $data['title'] = 'Master Data Kampus';
        $data['list'] = $this->M_Master->get_all_kampus(); 
        $this->render_view('admin/master_kampus', $data);
    }

public function master_kampus_add() {
    $data = [
        'nama_institusi' => $this->input->post('nama_institusi'),
        'kategori' => $this->input->post('kategori')
    ];
    
    if($data['nama_institusi'] && $data['kategori']) {
        $this->M_Master->insert_kampus($data);
        $this->session->set_flashdata('success', 'Data berhasil ditambahkan');
    }
    redirect('admin/master_kampus');
}

public function master_kampus_delete($id) {
    $this->M_Master->delete_kampus($id);
    $this->session->set_flashdata('success', 'Data berhasil dihapus');
    redirect('admin/master_kampus');
}
    
    // --- MASTER DATA JURUSAN ---
    public function master_jurusan() {
    $data['title'] = 'Master Data Jurusan';
    $data['list'] = $this->M_Master->get_all_jurusan();
    $this->render_view('admin/master_jurusan', $data);
}

public function master_jurusan_add() {
    $nama = $this->input->post('nama_jurusan');
    if($nama) {
        $this->M_Master->insert_jurusan(['nama_jurusan' => $nama]);
        $this->session->set_flashdata('success', 'Data berhasil ditambahkan');
    }
    redirect('admin/master_jurusan');
}

public function master_jurusan_delete($id) {
    $this->M_Master->delete_jurusan($id);
    $this->session->set_flashdata('success', 'Data berhasil dihapus');
    redirect('admin/master_jurusan');
}

    public function monitoring_harian()
	{
		$data['title'] = 'Monitoring Absensi Hari Ini';
		$today = date('Y-m-d');
				$data['absensi'] = $this->M_Absensi->get_monitoring_harian($today);
			
		$this->render_view('admin/monitoring_harian', $data);
	}

	public function broadcast_excel()
    {
        if(empty($_FILES['file_excel']['name'])){
            $this->session->set_flashdata('error', 'File belum dipilih');
            redirect('admin/broadcast');
        }

        $config['upload_path'] = './assets/uploads/';
        $config['allowed_types'] = 'csv|xls|xlsx'; 
        $config['max_size'] = 2048;
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('file_excel')) {
            $file_data = $this->upload->data();
            $file_path = './assets/uploads/' . $file_data['file_name'];
            
            $file = fopen($file_path, 'r');
            $success_count = 0;
            
            while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
                $no_hp = $row[0]; 
                $pesan = isset($row[2]) ? $row[2] : $this->input->post('pesan_default');
                
                if(!is_numeric(str_replace(['+','-',' '], '', $no_hp))) continue;

                $this->wa_client->send_message($no_hp, $pesan);
                $success_count++;
            }
            fclose($file);
            unlink($file_path);

            $this->session->set_flashdata('success', "Broadcast terkirim ke $success_count nomor.");
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        }
        redirect('admin/broadcast');
    }

	public function data_peserta()
    {
        $data['title'] = 'Data Semua Peserta';
        $data['list'] = $this->M_Admin->get_all_peserta_detailed();
        $this->render_view('admin/data_peserta', $data);
    }

    // --- MASTER FAKULTAS ---
    public function master_fakultas() {
			$data['title'] = 'Master Data Fakultas';
			$data['list'] = $this->M_Master->get_all_fakultas();
			$this->render_view('admin/master_fakultas', $data);
		}

		public function master_fakultas_add() {
			$nama = $this->input->post('nama_fakultas');
			if($nama) {
				$this->M_Master->insert_fakultas(['nama_fakultas' => $nama]);
				$this->session->set_flashdata('success', 'Fakultas berhasil ditambahkan');
			}
			redirect('admin/master_fakultas');
		}

		public function master_fakultas_delete($id) {
			$this->M_Master->delete_fakultas($id);
			$this->session->set_flashdata('success', 'Fakultas berhasil dihapus');
			redirect('admin/master_fakultas');
		}

	public function reset_password($user_id)
    {
        if (empty($user_id)) show_404();
        $new_password = password_hash('123456', PASSWORD_DEFAULT);
        $this->M_Admin->update_password($user_id, $new_password);
        $this->session->set_flashdata('success', 'Password berhasil direset menjadi: 123456');
        redirect($_SERVER['HTTP_REFERER']);
    }

	public function monitoring_absensi()
	{
		$tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
		$filter_status = $this->input->get('status');
		$query_results = $this->M_Absensi->get_monitoring_filter($tanggal);

		foreach ($query_results as $row) {
			// Logika penentuan status tampilan (Logic tetap, hanya sumber data berubah)
			if (!$row->absensi_status) {
				$row->display_status = 'Belum Absen';
				$row->label_class = 'badge-secondary';
			} elseif ($row->absensi_status == 'izin') {
				$row->display_status = 'Izin (' . strtoupper($row->jenis_izin) . ')';
				$row->label_class = 'badge-warning';
			} elseif ($row->absensi_status == 'telat' || ($row->jam_datang > '08:00:00' && $row->absensi_status == 'hadir')) {
				$row->display_status = 'Terlambat';
				$row->label_class = 'badge-danger';
			} elseif ($row->absensi_status == 'hadir') {
				$row->display_status = 'Hadir';
				$row->label_class = 'badge-success';
			} else {
				$row->display_status = ucfirst($row->absensi_status);
				$row->label_class = 'badge-info';
			}
		}

		// Filter status berdasarkan display_status
		if ($filter_status) {
			$query_results = array_filter($query_results, function($item) use ($filter_status) {
				if ($filter_status == 'masuk') return ($item->absensi_status == 'hadir' || $item->absensi_status == 'telat');
				if ($filter_status == 'izin') return ($item->absensi_status == 'izin');
				if ($filter_status == 'belum') return (empty($item->absensi_status));
				return true;
			});
		}

		$data['absensi'] = $query_results;
		$data['tanggal'] = $tanggal;
		$data['filter_status'] = $filter_status;
		$data['title'] = "Monitoring Absensi Harian";

		$this->render_view('admin/monitoring_absensi', $data);
	}

public function save_absen_manual()
{
    $user_id = $this->input->post('user_id');
    $tanggal = $this->input->post('tanggal');
    $jam_datang = $this->input->post('jam_datang');
    $jam_pulang = $this->input->post('jam_pulang');
    $status = $this->input->post('status');

    $existing = $this->M_Absensi->check_existing_absen($user_id, $tanggal);

    $data = [
        'user_id'    => $user_id,
        'tanggal'    => $tanggal,
        'jam_datang' => $jam_datang ?: NULL,
        'jam_pulang' => $jam_pulang ?: NULL,
        'status'     => $status,
        'keterangan' => 'Diinput manual oleh Admin'
    ];

    // Simpan via model
    $existing_id = $existing ? $existing->id : NULL;
    $this->M_Absensi->save_absen_manual($data, $existing_id);

    $this->session->set_flashdata('success', 'Berhasil memperbarui absensi manual.');
    redirect('admin/monitoring_absensi?tanggal=' . $tanggal);
}

	public function master_admin() {
		$data['title'] = 'Manajemen Akun Staff';
		$data['admins'] = $this->M_Admin->get_admins();
		$data['divisi'] = $this->M_Master->get_all_divisi(); // Ambil list divisi untuk dropdown
		$this->render_view('admin/master_admin', $data);
	}

	public function admin_add() {
		$role = $this->input->post('role'); // Tangkap role dari form (admin/mentor)
		$data = [
			'nama_lengkap' => $this->input->post('nama'),
			'email'        => $this->input->post('email'),
			'password'     => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
			'role'         => $role,
			'role_id'      => ($role == 'admin') ? 1 : 2, // 1: Admin, 2: Mentor
			'divisi_id'    => ($role == 'mentor') ? $this->input->post('divisi_id') : 6 // 6 = ID Divisi HC
		];
		
		$this->M_Admin->insert_admin($data);
		$this->session->set_flashdata('success', 'Akun staff berhasil ditambahkan');
		redirect('admin/master_admin');
	}

	public function admin_delete($id) {
			if ($id == $this->session->userdata('user_id')) {
				$this->session->set_flashdata('error', 'Tidak bisa menghapus akun sendiri!');
			} else {
				$this->M_Admin->delete_admin($id);
				$this->session->set_flashdata('success', 'Akun admin telah dihapus');
			}
			redirect('admin/master_admin');
		}

		// --- MASTER DATA DIVISI ---
	public function master_divisi() {
		$data['title'] = 'Master Data Divisi';
		$data['list'] = $this->M_Master->get_all_divisi();
		$this->render_view('admin/master_divisi', $data);
	}

	public function master_divisi_add() {
		$nama = $this->input->post('nama_divisi');
		if($nama) {
			$this->M_Master->insert_divisi(['nama_divisi' => $nama]);
			$this->session->set_flashdata('success', 'Divisi berhasil ditambahkan');
		}
		redirect('admin/master_divisi');
	}

	public function master_divisi_delete($id) {
		$this->M_Master->delete_divisi($id);
		$this->session->set_flashdata('success', 'Divisi berhasil dihapus');
		redirect('admin/master_divisi');
	}

	// --- MASTER DATA LOKASI (GEOFENCING) ---
	public function master_lokasi() {
		$data['title'] = 'Master Lokasi Absensi';
		$data['list'] = $this->M_Master->get_all_lokasi();
		$this->render_view('admin/master_lokasi', $data);
	}

	public function master_lokasi_add() {
		$data = [
			'nama_lokasi'  => $this->input->post('nama_lokasi'),
			'latitude'     => $this->input->post('latitude'),
			'longitude'    => $this->input->post('longitude'),
			'radius_meter' => $this->input->post('radius_meter')
		];
		
		if($data['nama_lokasi']) {
			$this->M_Master->insert_lokasi($data);
			$this->session->set_flashdata('success', 'Lokasi berhasil ditambahkan');
		}
		redirect('admin/master_lokasi');
	}

	public function master_lokasi_delete($id) {
		$this->M_Master->delete_lokasi($id);
		$this->session->set_flashdata('success', 'Lokasi berhasil dihapus');
		redirect('admin/master_lokasi');
	}
	public function laporan_global() {
		$data['title'] = 'Laporan Performa Divisi';
		$report = $this->M_Master->get_performance_report();
		$summary = $this->M_Master->get_kebutuhan_divisi_summary();

		$mapped_summary = [];
		foreach($summary as $s) {
			$mapped_summary[$s->id] = $s;
		}

		$data['report'] = $report;
		$data['summary_mapped'] = $mapped_summary;
		
		$this->render_view('admin/report_global', $data);
	}
}
