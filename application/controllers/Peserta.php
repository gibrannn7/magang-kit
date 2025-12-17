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
    }

    private function render_view($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/admin_template', $data);
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $today = date('Y-m-d');

        $data['absensi'] = $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $today])->row();
        $data['riwayat'] = $this->db->order_by('tanggal', 'DESC')->limit(5)->get_where('absensi', ['user_id' => $user_id])->result();
        $data['pendaftar'] = $this->db->get_where('pendaftar', ['user_id' => $user_id])->row();

        $data['title'] = 'Dashboard Peserta';
        $this->render_view('peserta/dashboard', $data);
    }

    public function absen_area()
    {
        $data['title'] = 'Lakukan Absensi';
        $this->render_view('peserta/absen_form', $data);
    }

    // --- PERBAIKAN UTAMA DISINI ---
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

            $user_id = $this->session->userdata('user_id');
            $today = date('Y-m-d');
            $is_izin = $this->input->post('is_izin'); 

            // --- LOGIC IZIN / SAKIT ---
            if ($is_izin === 'true') {
                $jenis_izin = $this->input->post('jenis_izin');
                $keterangan = $this->input->post('keterangan');

                if (empty($jenis_izin) || empty($keterangan)) {
                    throw new Exception('Jenis izin dan keterangan wajib diisi!');
                }

                $cek = $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $today])->row();
                if($cek) {
                    throw new Exception('Anda sudah input absen/izin hari ini.');
                }

                $this->db->insert('absensi', [
                    'user_id' => $user_id,
                    'tanggal' => $today,
                    'jam_datang' => $now, 
                    'status' => 'izin', 
                    'keterangan' => "[$jenis_izin] $keterangan"
                ]);

                $response['status'] = true;
                $response['message'] = 'Pengajuan Izin Berhasil Disimpan.';
                echo json_encode($response);
                return;
            }

            // --- LOGIC ABSEN HADIR ---
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

            $cek = $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $today])->row();

            if ($tipe === 'datang') {
                if ($cek) {
                    throw new Exception('Anda sudah absen datang hari ini.');
                }

                // Upload Foto
                $foto = $this->_save_base64_image($foto_base64, 'datang');
                if(!$foto) throw new Exception('Gagal menyimpan foto ke server.');

                $status = ($now > '08:00:00') ? 'telat' : 'hadir';

                $this->db->insert('absensi', [
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
                if(!$foto) throw new Exception('Gagal menyimpan foto ke server.');

                $this->db->update('absensi', [
                    'jam_pulang' => $now,
                    'lat_pulang' => $lat_user,
                    'long_pulang' => $long_user,
                    'foto_pulang' => $foto
                ], ['id' => $cek->id]);
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
        // (Biarkan kode ini seperti sebelumnya)
        require_once FCPATH . 'vendor/autoload.php';
        $user_id = $this->session->userdata('user_id');
        $pendaftar = $this->db->get_where('pendaftar', ['user_id' => $user_id])->row();

        if (!$pendaftar || $pendaftar->status !== 'selesai') {
            $this->session->set_flashdata('error', 'Program magang belum selesai!');
            redirect('peserta');
        }

        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tm = strtotime($pendaftar->tgl_mulai);
        $ts = strtotime($pendaftar->tgl_selesai);
        $tgl_mulai = date('j', $tm) . ' ' . $bulan[(int)date('n', $tm)] . ' ' . date('Y', $tm);
        $tgl_selesai = date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
        
        $data = [
            'nama' => strtoupper($pendaftar->nama),
            'periode' => $tgl_mulai . ' - ' . $tgl_selesai,
            'tanggal_sertifikat' => 'Serang, ' . date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y'),
            'background_path' => base_url('assets/templates/sertifikat.jpg') 
        ];

        $html = $this->load->view('laporan/pdf_sertifikat', $data, TRUE);
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true); 
        $options->set('defaultFont', 'Times-Roman');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Sertifikat_Magang_" . str_replace(' ', '_', $pendaftar->nama) . ".pdf", ["Attachment" => 1]);
    }
}
