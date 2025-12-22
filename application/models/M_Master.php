<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Master extends CI_Model {

    // --- LOGIKA MASTER KAMPUS/INSTITUSI ---
    public function get_all_kampus() {
        return $this->db->get('master_institusi')->result();
    }

    public function insert_kampus($data) {
        return $this->db->insert('master_institusi', $data);
    }

    public function delete_kampus($id) {
        return $this->db->delete('master_institusi', ['id' => $id]);
    }

    // --- LOGIKA MASTER FAKULTAS ---
    public function get_all_fakultas() {
        return $this->db->get('master_fakultas')->result();
    }

    public function insert_fakultas($data) {
        return $this->db->insert('master_fakultas', $data);
    }

    public function delete_fakultas($id) {
        return $this->db->delete('master_fakultas', ['id' => $id]);
    }

    // --- LOGIKA MASTER JURUSAN ---
    public function get_all_jurusan() {
        return $this->db->get('master_jurusan')->result();
    }

    public function insert_jurusan($data) {
        return $this->db->insert('master_jurusan', $data);
    }

    public function delete_jurusan($id) {
        return $this->db->delete('master_jurusan', ['id' => $id]);
    }
}
