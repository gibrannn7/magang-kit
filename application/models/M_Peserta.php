<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Peserta extends CI_Model {

    public function get_dashboard_data($user_id, $today) {
        return [
            'absensi'   => $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $today])->row(),
            'riwayat'   => $this->db->order_by('tanggal', 'DESC')->limit(5)->get_where('absensi', ['user_id' => $user_id])->result(),
            'pendaftar' => $this->db->get_where('pendaftar', ['user_id' => $user_id])->row()
        ];
    }

    public function get_data_sertifikat($user_id) {
        return $this->db->select('pendaftar.*, users.nama_lengkap')
                        ->join('users', 'users.id = pendaftar.user_id')
                        ->get_where('pendaftar', ['pendaftar.user_id' => $user_id])
                        ->row();
    }
}
