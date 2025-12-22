<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Absensi extends CI_Model {

    /**
     * Mengambil data rekap absensi per user untuk laporan PDF
     */
    public function get_absensi_by_user($user_id)
    {
        return $this->db->order_by('tanggal', 'ASC')
            ->get_where('absensi', ['user_id' => $user_id])
            ->result();
    }

    /**
     * Mengambil monitoring absensi harian (Task 4)
     */
    public function get_monitoring_harian($today)
    {
        return $this->db->select('absensi.*, users.nama_lengkap, pendaftar.institusi')
            ->from('absensi')
            ->join('users', 'users.id = absensi.user_id')
            ->join('pendaftar', 'pendaftar.user_id = users.id')
            ->where('absensi.tanggal', $today)
            ->get()
            ->result();
    }

    /**
     * Mengambil monitoring absensi dengan filter (Task: Monitoring Absensi)
     */
    public function get_monitoring_filter($tanggal)
    {
        $this->db->select('u.id as user_id, p.nama, p.institusi, a.id as absensi_id, a.jam_datang, a.jam_pulang, a.status as absensi_status, a.bukti_izin, a.keterangan, a.jenis_izin');
        $this->db->from('users u');
        $this->db->join('pendaftar p', 'u.id = p.user_id'); 
        $this->db->join('absensi a', "u.id = a.user_id AND a.tanggal = '$tanggal'", 'left');
        $this->db->where('u.role', 'peserta');
        $this->db->where('p.status', 'diterima'); 
        return $this->db->get()->result();
    }

    /**
     * Cek keberadaan data absensi untuk update manual
     */
    public function check_existing_absen($user_id, $tanggal)
    {
        return $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $tanggal])->row();
    }

    /**
     * Simpan atau Update Absensi Manual
     */
    public function save_absen_manual($data, $existing_id = NULL)
    {
        if ($existing_id) {
            $this->db->where('id', $existing_id);
            return $this->db->update('absensi', $data);
        } else {
            return $this->db->insert('absensi', $data);
        }
    }
	public function insert_absen($data) {
        return $this->db->insert('absensi', $data);
    }

    public function update_absen($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('absensi', $data);
    }

    public function insert_izin_batch($data_batch) {
        return $this->db->insert_batch('absensi', $data_batch);
    }
}
