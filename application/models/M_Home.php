<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Home extends CI_Model {

    /**
     * Mengambil semua data institusi/kampus
     */
    public function get_all_institusi()
    {
        return $this->db->get('master_institusi')->result();
    }

    /**
     * Mengambil semua data fakultas
     */
    public function get_all_fakultas()
    {
        return $this->db->get('master_fakultas')->result();
    }

    /**
     * Mengambil semua data jurusan
     */
    public function get_all_jurusan()
    {
        return $this->db->get('master_jurusan')->result();
    }

    /**
     * Menangani transaksi pendaftaran (Insert pendaftar & batch dokumen)
     * @param array $data_pendaftar
     * @param array $data_dokumen
     * @return boolean
     */
    public function simpan_pendaftaran($data_pendaftar, $data_dokumen)
    {
        $this->db->trans_start();

        // Insert ke tabel pendaftar
        $this->db->insert('pendaftar', $data_pendaftar);
        $pendaftar_id = $this->db->insert_id();

        // Siapkan data dokumen dengan pendaftar_id yang baru saja di-insert
        $final_dokumen = [];
        foreach ($data_dokumen as $dok) {
            $dok['pendaftar_id'] = $pendaftar_id;
            $final_dokumen[] = $dok;
        }

        // Insert Batch ke tabel dokumen
        $this->db->insert_batch('dokumen', $final_dokumen);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
