<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peserta extends CI_Controller {

    const LAT_KANTOR = -6.171144960493601;
    const LONG_KANTOR = 106.1609483232592;
    const MAX_RADIUS_METER = 100;

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'peserta') {
            redirect('auth/login');
        }
        date_default_timezone_set('Asia/Jakarta'); 

        // FIX ERROR: Load semua model yang dibutuhkan di sini
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

		// Mengambil data melalui model (Logika terpusat di M_Peserta)
		$dashboard = $this->M_Peserta->get_dashboard_data($user_id, $today);
		
		$data['absensi']    = $dashboard['absensi'];
		$data['riwayat']    = $dashboard['riwayat'];
		$data['pendaftar']  = $dashboard['pendaftar']; // Berisi status & file_surat_balasan
		$data['title']      = 'Dashboard Peserta';

		$this->render_view('peserta/dashboard', $data);
	}

    public function absen_area()
    {
        $data['title'] = 'Lakukan Absensi';
        $this->render_view('peserta/absen_form', $data);
    }
	
    public function submit_absen()
{
    // Set Header JSON agar browser tahu ini respon data, bukan HTML
    header('Content-Type: application/json');

    // Siapkan response default + Token CSRF Baru (PENTING untuk AJAX)
    $response = [
        'status' => false, 
        'message' => 'Unknown Error',
        'csrf_token' => $this->security->get_csrf_hash() 
    ];

    try {
        $user_id = $this->session->userdata('user_id');
        $is_izin = $this->input->post('is_izin');

        // --- LOGIC IZIN / SAKIT ---
        if ($is_izin === 'true') {
            $tgl_mulai   = $this->input->post('tgl_mulai');
            $tgl_selesai = $this->input->post('tgl_selesai');
            $jenis_izin  = $this->input->post('jenis_izin');
            $keterangan  = $this->input->post('keterangan');

            // Validasi input izin
            if (empty($tgl_mulai) || empty($tgl_selesai) || empty($jenis_izin) || empty($keterangan)) {
                throw new Exception('Semua data izin wajib diisi!');
            }

            // 1. Upload Bukti Izin
            $config['upload_path']   = './assets/uploads/absensi/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['encrypt_name']  = TRUE;
            $this->load->library('upload', $config);

            $nama_file_bukti = NULL;
            if ($this->upload->do_upload('bukti_file')) {
                $nama_file_bukti = $this->upload->data('file_name');
            } else {
                throw new Exception('Gagal upload bukti: ' . $this->upload->display_errors('',''));
            }

            // 2. Logika Looping Tanggal
            $begin = new DateTime($tgl_mulai);
            $end   = new DateTime($tgl_selesai);
            $end->modify('+1 day'); // Agar tanggal selesai ikut terhitung

            $interval = DateInterval::createFromDateString('1 day');
            $period   = new DatePeriod($begin, $interval, $end);

            // Persiapkan data batch
            $data_batch = [];
            foreach ($period as $dt) {
                $curr_date = $dt->format("Y-m-d");
                
                // Lewati jika Sabtu/Minggu (Optional, silakan hapus jika tetap ingin dicatat)
                if ($dt->format('N') >= 6) continue;

                // REFACTOR: Gunakan check_existing_absen dari M_Absensi
                $exists = $this->M_Absensi->check_existing_absen($user_id, $curr_date);
                if (!$exists) {
                    $data_batch[] = [
                        'user_id' => $user_id,
                        'tanggal' => $curr_date,
                        'status' => 'izin',
                        'jenis_izin' => $jenis_izin,
                        'keterangan' => $keterangan,
                        'bukti_izin' => $nama_file_bukti,
                        'jam_datang' => '00:00:00',
                        'jam_pulang' => '00:00:00'
                    ];
                }
            }

            // Insert batch jika ada data
            $count_inserted = 0;
            if (!empty($data_batch)) {
                $count_inserted = $this->M_Absensi->insert_izin_batch($data_batch);
            }

            $response['status'] = true;
            $response['message'] = "Berhasil mengajukan izin selama $count_inserted hari kerja.";
            echo json_encode($response);
            return;
        }

        // --- LOGIC ABSEN HADIR ---
        // 1. Cek Hari (Sabtu=6, Minggu=7 Libur)
        $hari_ini = date('N'); 
        if ($hari_ini >= 6) { 
            throw new Exception('Hari Libur: Absensi tidak dapat dilakukan.');
        }

        // 2. Cek Jam
        $now = date('H:i:s');
        // Untuk testing saya set 06:00, sesuaikan kebutuhan
        if ($now < '06:00:00') {
            throw new Exception('Absensi belum dibuka. Dimulai pukul 06:00 WIB.');
        }

        $today = date('Y-m-d');

        $lat_user = $this->input->post('latitude');
        $long_user = $this->input->post('longitude');
        $tipe = $this->input->post('tipe');
        $foto_base64 = $this->input->post('foto');

        // Validasi Input
        if (empty($lat_user) || empty($long_user) || empty($foto_base64)) {
            throw new Exception('Lokasi atau Foto kosong. Pastikan GPS aktif dan izin kamera diberikan.');
        }

        // Validasi Jarak
        $jarak_meter = $this->haversineGreatCircleDistance(self::LAT_KANTOR, self::LONG_KANTOR, $lat_user, $long_user);
        if ($jarak_meter > self::MAX_RADIUS_METER) {
            throw new Exception('Jarak terlalu jauh (' . round($jarak_meter) . 'm). Wajib di area kantor (Max ' . self::MAX_RADIUS_METER . 'm).');
        }

        // REFACTOR: Gunakan M_Absensi untuk cek data
        $cek = $this->M_Absensi->check_existing_absen($user_id, $today);

        if ($tipe === 'datang') {
            if ($cek) {
                throw new Exception('Anda sudah absen datang hari ini.');
            }

            // Upload Foto
            $foto = $this->_save_base64_image($foto_base64, 'datang');
            if (!$foto) throw new Exception('Gagal menyimpan foto ke server.');

            $status = ($now > '08:00:00') ? 'telat' : 'hadir';

            // REFACTOR: Gunakan M_Absensi
            $this->M_Absensi->insert_absen([
                'user_id' => $user_id,
                'tanggal' => $today,
                'jam_datang' => $now,
                'lat_datang' => $lat_user,
                'long_datang' => $long_user,
                'foto_datang' => $foto,
                'status' => $status
            ]);

        } elseif ($tipe === 'pulang') {
            if (!$cek) throw new Exception('Anda belum absen datang.');
            if ($cek->jam_pulang != NULL) throw new Exception('Anda sudah absen pulang hari ini.');

            $jam_pulang_min = ($hari_ini == 5) ? '16:30:00' : '16:00:00'; 
            if ($now < $jam_pulang_min) {
                // throw new Exception('Belum jam pulang (' . $jam_pulang_min . ').'); // Uncomment jika ketat
            }

            $foto = $this->_save_base64_image($foto_base64, 'pulang');
            if (!$foto) throw new Exception('Gagal menyimpan foto ke server.');

            // REFACTOR: Gunakan M_Absensi
            $this->M_Absensi->update_absen($cek->id, [
                'jam_pulang' => $now,
                'lat_pulang' => $lat_user,
                'long_pulang' => $long_user,
                'foto_pulang' => $foto
            ]);
        }

        $response['status'] = true;
        $response['message'] = 'Absensi Berhasil! Jarak: ' . round($jarak_meter) . 'm';

    } catch (Exception $e) {
        $response['status'] = false;
        $response['message'] = $e->getMessage();
    }

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
                return false; // Gagal buat folder
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

        // --- PERBAIKAN HANDLING GAMBAR DISINI ---
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
            'periode_text' => "Sebagai peserta Praktikum Profesi Lapangan (PPL) di kantor Badan Pusat Statistik Provinsi Banten mulai tanggal $tgl_mulai_indo s.d $tgl_selesai_indo.",
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

        // REFACTOR: Gunakan M_Auth
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

        // REFACTOR: Gunakan M_Auth
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
}
