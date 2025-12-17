<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect($this->session->userdata('role') == 'admin' ? 'admin' : 'peserta');
        }

        // --- LOGIC CAPTCHA (1-9, Penjumlahan/Pengurangan) ---
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $operator = rand(0, 1) ? '+' : '-';
        
        // Hitung hasil
        $result = ($operator == '+') ? ($num1 + $num2) : ($num1 - $num2);
        
        // Simpan jawaban benar ke session untuk validasi nanti
        $this->session->set_userdata('captcha_answer', $result);
        
        // Kirim teks soal ke view
        $data['captcha_text'] = "$num1 $operator $num2 = ?";

        $this->load->view('auth/login', $data);
    }

    public function process_login()
    {
        // 1. Validasi Captcha Terlebih Dahulu
        $input_captcha = $this->input->post('captcha');
        $real_captcha  = $this->session->userdata('captcha_answer');

        if ($input_captcha === NULL || $input_captcha != $real_captcha) {
            $this->session->set_flashdata('error', 'Hasil penjumlahan/pengurangan salah!');
            redirect('auth/login');
            return;
        }

        // 2. Lanjut ke Validasi User
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password');

        $user = $this->db->get_where('users', ['username' => $username])->row();

        if ($user) {
            // Verifikasi Hash Password
            if (password_verify($password, $user->password)) {
                
                // --- TASK 3: AMBIL FOTO PROFIL DARI BERKAS PENDAFTAR ---
                $foto_profil = null;
                
                // Cek apakah dia peserta (ada data pendaftar)
                $pendaftar = $this->db->get_where('pendaftar', ['user_id' => $user->id])->row();
                
                if ($pendaftar) {
                    $doc_foto = $this->db->get_where('dokumen', [
                        'pendaftar_id' => $pendaftar->id, 
                        'jenis_dokumen' => 'foto'
                    ])->row();

                    if ($doc_foto) {
                        $foto_profil = $doc_foto->file_path;
                    }
                }

                $sess_data = [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                    'nama_lengkap' => $user->nama_lengkap,
                    'foto_profil' => $foto_profil, // Simpan ke session
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($sess_data);

                // Update Last Login
                $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $user->id]);

                // Hapus session captcha
                $this->session->unset_userdata('captcha_answer');

                redirect($user->role == 'admin' ? 'admin' : 'peserta');
            } else {
                $this->session->set_flashdata('error', 'Password salah!');
                redirect('auth/login');
            }
        } else {
            $this->session->set_flashdata('error', 'Username tidak ditemukan!');
            redirect('auth/login');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    // Fungsi utility untuk reset password (hanya untuk development/debug)
    public function fix_password()
    {
        $username = 'admin'; 
        $password_baru = 'admin123'; 
        
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);
        
        $this->db->where('username', $username);
        $this->db->update('users', ['password' => $hash]);
        
        echo "<h1>Sukses!</h1>";
        echo "Password reset done.";
    }
}
