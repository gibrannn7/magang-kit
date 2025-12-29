<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        // Load model M_Auth
        $this->load->model('M_Auth');
		$this->load->helper('captcha');
    }

    public function login()
    {
        // 1. Jika sudah login, arahkan ke dashboard masing-masing
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            // PERBAIKAN: Hapus /dashboard karena fungsinya tidak ada, gunakan default index
            redirect($role == 'admin' ? 'admin' : 'peserta');
        }

        // 2. Set Validasi Form
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');
        $this->form_validation->set_rules('captcha', 'Captcha', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $data['captcha_img'] = $this->_generate_captcha();
            $this->load->view('auth/login', $data);
        } else {
            $email      = $this->input->post('email');
            $password   = $this->input->post('password');
            $captcha_in = $this->input->post('captcha');
            $sess_cap   = $this->session->userdata('captcha_word');

            // 3. Verifikasi Captcha
            if (strtolower($captcha_in) !== strtolower($sess_cap)) {
                $this->session->set_flashdata('error', 'Kode Captcha tidak sesuai!');
                redirect('auth/login');
                return;
            }

            // 4. Cek User via Model
            $user = $this->M_Auth->get_user_by_email($email);

            if ($user) {
                if (password_verify($password, $user->password)) {
                    $session_data = [
                        'user_id'   => $user->id,
                        'nama_lengkap' => $user->nama_lengkap,
                        'email'     => $user->email,
                        'role'      => $user->role,
                        'logged_in' => TRUE
                    ];
                    $this->session->set_userdata($session_data);
                    
                    // PERBAIKAN: Redirect ke controller utama (index)
                    redirect($user->role == 'admin' ? 'admin' : 'peserta');
                } else {
                    $this->session->set_flashdata('error', 'Password salah!');
                    redirect('auth/login');
                }
            } else {
                $this->session->set_flashdata('error', 'Email tidak terdaftar!');
                redirect('auth/login');
            }
        }
    }
	
	private function _generate_captcha()
{
    // Hapus file captcha lama agar tidak memenuhi storage
    $this->load->helper('file');
    delete_files('./assets/img/captcha/');

    $vals = [
        'img_path'      => './assets/img/captcha/',
        'img_url'       => base_url('assets/img/captcha/'),
        'img_width'     => 450, // Diperlebar sedikit agar teks tidak terpotong
        'img_height'    => 100, // Dipertinggi agar font bisa maksimal
        'expiration'    => 7200,
        'word_length'   => 5,
        'font_size'     => 40,  // Ukuran font besar
        'img_id'        => 'captcha-img',
        'pool'          => '0123456789abcdefghijklmnopqrstuvwxyz',
        'font_path'     => FCPATH . 'system/fonts/texb.ttf', 
        
        'colors'        => [
            'background' => [249, 250, 251], // bg-gray-50
            'border'     => [229, 231, 235], // border-gray-200
            'text'       => [0, 51, 102],    // bps-blue
            'grid'       => [200, 200, 200]  // Garis bantu halus
        ]
    ];

    $cap = create_captcha($vals);
    $this->session->set_userdata('captcha_word', $cap['word']);
    return $cap['image'];
}

public function refresh_captcha()
{
    echo $this->_generate_captcha();
}

//     public function process_login()
// {
//     // 1. Validasi Captcha (CODE LAMA TETAP SAMA)
//     $input_captcha = $this->input->post('captcha');
//     $real_captcha  = $this->session->userdata('captcha_answer');

//     if ($input_captcha === NULL || $input_captcha != $real_captcha) {
//         $this->session->set_flashdata('error', 'Hasil penjumlahan/pengurangan salah!');
//         redirect('auth/login');
//         return;
//     }

//     // 2. UPDATE: Lanjut ke Validasi User menggunakan EMAIL
//     $email    = $this->input->post('email', TRUE); // Ambil input email
//     $password = $this->input->post('password');

//     // Cari user berdasarkan EMAIL
//     $user = $this->db->get_where('users', ['email' => $email])->row();

//     if ($user) {
//         // Verifikasi Hash Password
//         if (password_verify($password, $user->password)) {
            
//             // --- LOGIC FOTO PROFIL (CODE LAMA TETAP SAMA) ---
//             $foto_profil = null;
//             $pendaftar = $this->db->get_where('pendaftar', ['user_id' => $user->id])->row();
            
//             if ($pendaftar) {
//                 $doc_foto = $this->db->get_where('dokumen', [
//                     'pendaftar_id' => $pendaftar->id, 
//                     'jenis_dokumen' => 'foto'
//                 ])->row();

//                 if ($doc_foto) {
//                     $foto_profil = $doc_foto->file_path;
//                 }
//             }

//             $sess_data = [
//                 'user_id' => $user->id,
//                 'email' => $user->email,       // Tambahan session email
//                 'role' => $user->role,
//                 'nama_lengkap' => $user->nama_lengkap,
//                 'foto_profil' => $foto_profil,
//                 'logged_in' => TRUE
//             ];
//             $this->session->set_userdata($sess_data);

//             // Update Last Login
//             $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $user->id]);

//             $this->session->unset_userdata('captcha_answer');
//             redirect($user->role == 'admin' ? 'admin' : 'peserta');
//         } else {
//             $this->session->set_flashdata('error', 'Password salah!');
//             redirect('auth/login');
//         }
//     } else {
//         $this->session->set_flashdata('error', 'Email tidak terdaftar!');
//         redirect('auth/login');
//     }
// }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    public function fix_password() {
		$email = 'admin@bps.go.id'; 
        $data = ['password' => password_hash('admin123', PASSWORD_DEFAULT)];
        
		// REFACTOR: Gunakan M_Auth
		$this->M_Auth->update_user_by_email($email, $data);
		echo "Password admin berhasil direset menggunakan Email.";
	}
}
