<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peserta extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'peserta') {
            redirect('auth/login');
        }
        date_default_timezone_set('Asia/Jakarta'); 

        $this->load->model('M_Peserta');
        $this->load->model('M_Absensi');
        $this->load->model('M_Auth'); 
    }

    private function render_view($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/admin_template', $data);
    }

    public function index()
	{
		$user_id = $this->session->userdata('user_id');
		$today = date('Y-m-d');

		$dashboard = $this->M_Peserta->get_dashboard_data($user_id, $today);
		
		// TAMBAHKAN: Ambil detail lokasi untuk peta
		$this->db->select('master_lokasi.*');
		$this->db->from('users');
		$this->db->join('master_lokasi', 'master_lokasi.id = users.lokasi_id');
		$this->db->where('users.id', $user_id);
		$data['lokasi_absen'] = $this->db->get()->row();

		$data['absensi']    = $dashboard['absensi'];
		$data['riwayat']    = $dashboard['riwayat'];
		$data['pendaftar']  = $dashboard['pendaftar'];
		$data['title'] = $this->session->userdata('nama_lengkap') . ' | ' . strtoupper($this->session->userdata('role'));

		$this->render_view('peserta/dashboard', $data);
	}

    public function absen_area()
    {
        $data['title'] = 'Lakukan Absensi';
        $this->render_view('peserta/absen_form', $data);
    }
	
   public function submit_absen()
{
    $user_id = $this->session->userdata('user_id');
    $is_izin = $this->input->post('is_izin') === 'true';

    // Logika untuk Izin/Sakit (Menggunakan Upload File Fisik)
    if ($is_izin) {
        $config['upload_path']   = './assets/uploads/absensi/';
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['encrypt_name']  = TRUE;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('bukti_file')) {
            $response = ['status' => false, 'message' => $this->upload->display_errors('', '')];
        } else {
            $file = $this->upload->data('file_name');
            $data_izin = [
                'user_id'     => $user_id,
                'tanggal'     => $this->input->post('tgl_mulai'),
                'status'      => 'izin',
                'jenis_izin'  => $this->input->post('jenis_izin'),
                'keterangan'  => $this->input->post('keterangan'),
                'foto_datang' => $file
            ];
            $this->M_Absensi->save_absen($data_izin);
            $response = ['status' => true, 'message' => 'Permohonan izin berhasil dikirim'];
        }
    } 
    else {
        $tipe = $this->input->post('tipe');
        $lat  = $this->input->post('lat'); // Menggunakan 'lat' sesuai input JS nanti
        $long = $this->input->post('long');
        $foto_base64 = $this->input->post('foto');

        // Ambil data parameter Geofencing
        $user = $this->db->get_where('users', ['id' => $user_id])->row();
        $lokasi = $this->db->get_where('master_lokasi', ['id' => $user->lokasi_id])->row();

        if (!$lokasi) {
            $response = ['status' => false, 'message' => 'Titik lokasi absen belum ditentukan admin.'];
        } else {
            // Hitung Jarak (Logika Anda tetap terjaga)
            $jarak = $this->getDistanceBetweenPointsNew($lat, $long, $lokasi->latitude, $lokasi->longitude);

            if ($jarak > $lokasi->radius_meter) {
                $response = ['status' => false, 'message' => 'Anda berada di luar radius (' . round($jarak) . 'm).'];
            } else {
                // Simpan Foto dari Base64 Webcam
                $foto_name = $this->_save_base64_image($foto_base64, $tipe);
                
                if (!$foto_name) {
                    $response = ['status' => false, 'message' => 'Gagal menyimpan foto snapshot.'];
                } else {
                    $data_absen = ['user_id' => $user_id, 'tanggal' => date('Y-m-d')];

                    if ($tipe == 'datang') {
                        $data_absen['jam_datang']  = date('H:i:s');
                        $data_absen['lat_datang']  = $lat;
                        $data_absen['long_datang'] = $long;
                        $data_absen['foto_datang'] = $foto_name;
                        $data_absen['status']      = (date('H:i:s') > '08:00:00') ? 'telat' : 'hadir';
                        $this->M_Absensi->save_absen($data_absen);
                    } else {
                        $data_update = [
                            'jam_pulang'  => date('H:i:s'),
                            'lat_pulang'  => $lat,
                            'long_pulang' => $long,
                            'foto_pulang' => $foto_name
                        ];
                        $this->M_Absensi->update_absen($user_id, date('Y-m-d'), $data_update);
                    }
                    $response = ['status' => true, 'message' => 'Absensi ' . $tipe . ' berhasil dicatat!'];
                }
            }
        }
    }
    $response['csrf_token'] = $this->security->get_csrf_hash();

    echo json_encode($response);
}

    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    private function _save_base64_image($base64_string, $prefix) {
        // Cek apakah string valid
        if (strpos($base64_string, ',') !== false) {
            $image_parts = explode(",", $base64_string);
            $image_base64 = base64_decode($image_parts[1]);
        } else {
            $image_base64 = base64_decode($base64_string);
        }

        $filename = $prefix . '_' . uniqid() . '.png';
        // Gunakan FCPATH agar path absolute server terbaca dengan benar
        $folder = FCPATH . 'assets/uploads/absensi/';
        
        // Buat folder jika belum ada
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0777, true)) {
                return false;
            }
        }

        if(file_put_contents($folder . $filename, $image_base64)){
            return $filename;
        }
        return false;
    }

    public function download_sertifikat()
    {
        require_once FCPATH . 'vendor/autoload.php';
        $user_id = $this->session->userdata('user_id');
        $pendaftar = $this->M_Peserta->get_data_sertifikat($user_id);

        if (!$pendaftar || $pendaftar->status !== 'selesai') {
            $this->session->set_flashdata('error', 'Program magang belum selesai!');
            redirect('peserta');
        }

        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tm = strtotime($pendaftar->tgl_mulai);
        $ts = strtotime($pendaftar->tgl_selesai);
        
        $tgl_mulai_indo = date('j', $tm) . ' ' . $bulan[(int)date('n', $tm)] . ' ' . date('Y', $tm);
        $tgl_selesai_indo = date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
        $tgl_sertif_indo = 'Serang, ' . date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');

        $path_img = FCPATH . 'assets/templates/sertifikat_clean.jpg';
        
        // Cek apakah file ada?
        if (file_exists($path_img)) {
            $type = pathinfo($path_img, PATHINFO_EXTENSION);
            $data_img = file_get_contents($path_img);
            $base64_bg = 'data:image/' . $type . ';base64,' . base64_encode($data_img);
        } else {
            // Fallback jika gambar tidak ditemukan (agar tidak error blank page)
            $base64_bg = ''; 
            log_message('error', 'Sertifikat BG tidak ditemukan di: ' . $path_img);
        }

        $data = [
            'nama' => strtoupper($pendaftar->nama),
            'periode_text' => "Sebagai peserta Praktikum Profesi Lapangan (PPL) di kantor Krakatau Information Technology(KIT) mulai tanggal $tgl_mulai_indo s.d $tgl_selesai_indo.",
            'tanggal_sertifikat' => $tgl_sertif_indo,
            'background_base64' => $base64_bg // Kirim variable ini
        ];

        $html = $this->load->view('laporan/pdf_sertifikat', $data, TRUE);
        
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Sertifikat_Magang_" . str_replace(' ', '_', $pendaftar->nama) . ".pdf", ["Attachment" => 0]); 
    }
	public function ganti_password()
    {
        $data['title'] = 'Ganti Password';
        $this->render_view('peserta/ganti_password', $data);
    }

    public function process_ganti_password()
    {
        $old_pass = trim($this->input->post('old_password', FALSE));
        $new_pass = trim($this->input->post('new_password', FALSE));
        $conf_pass = trim($this->input->post('conf_password', FALSE));
        $user_id = $this->session->userdata('user_id');
        $user = $this->M_Auth->get_user_by_id($user_id);

        if (!$user || !password_verify($old_pass, $user->password)) {
            $this->session->set_flashdata('error', 'Password lama salah!');
            redirect('peserta/ganti_password');
            return;
        }

        if ($new_pass !== $conf_pass || strlen($new_pass) < 6) {
            $this->session->set_flashdata('error', 'Password baru tidak cocok atau terlalu pendek!');
            redirect('peserta/ganti_password');
            return;
        }

        $this->M_Auth->update_user($user_id, ['password' => password_hash($new_pass, PASSWORD_DEFAULT)]);

        $this->session->set_flashdata('success', 'Password berhasil diperbarui!');
        redirect('peserta/ganti_password');
    }

    // --- TASK 5: RIWAYAT LENGKAP ABSENSI ---
    public function riwayat_absensi()
    {
        $user_id = $this->session->userdata('user_id');
        $data['absensi'] = $this->M_Absensi->get_absensi_by_user($user_id);
        $data['title'] = 'Riwayat Absensi Lengkap';
        $this->render_view('peserta/riwayat_absensi', $data);
    }
	/**
     * Menghitung jarak antara dua titik koordinat (Haversine Formula)
     * @param float $latitude1, $longitude1 (Posisi User dari Browser/GPS)
     * @param float $latitude2, $longitude2 (Posisi Kantor dari Database)
     * @return float Jarak dalam satuan Meter
     */
    private function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
    {
        // Validasi jika input kosong
        if (empty($latitude1) || empty($longitude1) || empty($latitude2) || empty($longitude2)) {
            return 999999; // Return jarak sangat jauh agar akses ditolak
        }

        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        
        return $meters;
    }
}
