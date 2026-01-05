<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Admin extends CI_Model {

    // --- DASHBOARD & STATISTIK ---
    public function get_stats_pendaftaran() {
        return [
            'total'   => $this->db->count_all('pendaftar'),
            'pending' => $this->db->where('status', 'pending')->count_all_results('pendaftar'),
            'aktif'   => $this->db->where('status', 'diterima')->count_all_results('pendaftar'),
            'selesai' => $this->db->where('status', 'selesai')->count_all_results('pendaftar')
        ];
    }
	

    public function get_stats_absensi_today($today) {
        return [
            'hadir' => $this->db->where(['tanggal' => $today, 'status' => 'hadir'])->count_all_results('absensi'),
            'telat' => $this->db->where(['tanggal' => $today, 'status' => 'telat'])->count_all_results('absensi'),
            'izin'  => $this->db->where(['tanggal' => $today, 'status' => 'izin'])->count_all_results('absensi')
        ];
    }

    // --- DATA PENDAFTAR ---
    public function get_all_pendaftar() {
        return $this->db->select('pendaftar.*, users.email as akun_user')
            ->join('users', 'users.id = pendaftar.user_id', 'left')
            ->order_by('pendaftar.id', 'DESC')
            ->get('pendaftar')
            ->result();
    }

    public function get_pendaftar_by_id($id) {
        return $this->db->get_where('pendaftar', ['id' => $id])->row();
    }

    public function get_dokumen_by_pendaftar($pendaftar_id) {
        return $this->db->get_where('dokumen', ['pendaftar_id' => $pendaftar_id])->result();
    }

    // --- PROSES VERIFIKASI (TRANSACTION) ---
    public function proses_verifikasi_diterima($id, $update_pendaftar, $data_user) {
        $this->db->trans_start();
        
        // 1. Buat User jika belum ada
        $pendaftar = $this->get_pendaftar_by_id($id);
        if ($pendaftar->user_id === NULL) {
            $this->db->insert('users', $data_user);
            $update_pendaftar['user_id'] = $this->db->insert_id();
        }

        // 2. Update status pendaftar & simpan nama file surat
        $this->db->update('pendaftar', $update_pendaftar, ['id' => $id]);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update_status_pendaftar($id, $status) {
        return $this->db->update('pendaftar', ['status' => $status], ['id' => $id]);
    }

    // --- MASTER DATA ---
    public function get_master($table) {
        return $this->db->get($table)->result();
    }

    public function insert_master($table, $data) {
        return $this->db->insert($table, $data);
    }

    public function delete_master($table, $id) {
        return $this->db->delete($table, ['id' => $id]);
    }

    // --- MONITORING ABSENSI ---
    public function get_monitoring_absensi($tanggal) {
        $this->db->select('u.id as user_id, p.nama, p.institusi, a.id as absensi_id, a.jam_datang, a.jam_pulang, a.status as absensi_status, a.bukti_izin, a.keterangan, a.jenis_izin');
        $this->db->from('users u');
        $this->db->join('pendaftar p', 'u.id = p.user_id'); 
        $this->db->join('absensi a', "u.id = a.user_id AND a.tanggal = '$tanggal'", 'left');
        $this->db->where('u.role', 'peserta');
        $this->db->where('p.status', 'diterima'); 
        return $this->db->get()->result();
    }

    public function save_absen_manual($data) {
        $existing = $this->db->get_where('absensi', [
            'user_id' => $data['user_id'], 
            'tanggal' => $data['tanggal']
        ])->row();

        if ($existing) {
            $this->db->where('id', $existing->id);
            return $this->db->update('absensi', $data);
        } else {
            return $this->db->insert('absensi', $data);
        }
    }

    // --- MANAJEMEN USER/ADMIN ---
    public function get_users_by_role($role) {
        return $this->db->get_where('users', ['role' => $role])->result();
    }

    public function reset_password($user_id, $new_password) {
        return $this->db->update('users', ['password' => $new_password], ['id' => $user_id]);
    }

	public function get_pendaftar_by_user($user_id) {
        return $this->db->get_where('pendaftar', ['user_id' => $user_id])->row();
    }

    public function get_all_peserta_detailed() {
        return $this->db->select('pendaftar.*, users.email')
            ->join('users', 'users.id = pendaftar.user_id', 'left')
            ->order_by('pendaftar.id', 'DESC')
            ->get('pendaftar')
            ->result();
    }

    public function get_admins() {
		return $this->db->select('users.*, master_divisi.nama_divisi')
			->from('users')
			->join('master_divisi', 'master_divisi.id = users.divisi_id', 'left')
			->where_in('role', ['admin', 'mentor'])
			->get()
			->result();
	}

    public function insert_admin($data) {
        return $this->db->insert('users', $data);
    }

    public function delete_admin($id) {
        return $this->db->delete('users', ['id' => $id]);
    }

    public function update_password($user_id, $new_password_hash) {
        return $this->db->update('users', ['password' => $new_password_hash], ['id' => $user_id]);
    }
}
