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
	
	// --- LOGIKA MASTER DIVISI ---
	public function get_all_divisi() {
        return $this->db->get('master_divisi')->result();
    }

	public function insert_divisi($data) {
		return $this->db->insert('master_divisi', $data);
	}

	public function delete_divisi($id) {
		return $this->db->delete('master_divisi', ['id' => $id]);
	}

	// --- LOGIKA MASTER LOKASI ---
	public function get_all_lokasi() {
		return $this->db->get('master_lokasi')->result();
	}

	public function insert_lokasi($data) {
		return $this->db->insert('master_lokasi', $data);
	}

	public function delete_lokasi($id) {
		return $this->db->delete('master_lokasi', ['id' => $id]);
	}

	public function get_requests_by_divisi($divisi_id) {
        return $this->db->get_where('permintaan_magang', ['divisi_id' => $divisi_id])->result();
    }

    public function insert_request($data) {
        return $this->db->insert('permintaan_magang', $data);
    }

    public function delete_request($id) {
        return $this->db->delete('permintaan_magang', ['id' => $id]);
    }

    public function get_kebutuhan_divisi_summary() {
		// Query ini akan menghasilkan 1 baris per divisi dengan total kebutuhan yang sudah dijumlahkan
		$this->db->select('
			d.id, 
			d.nama_divisi, 
			(SELECT IFNULL(SUM(jumlah), 0) FROM permintaan_magang WHERE divisi_id = d.id) as total_diminta,
			(SELECT COUNT(*) FROM users WHERE divisi_id = d.id AND role_id = 3) as total_terisi
		');
		$this->db->from('master_divisi d');
		$this->db->group_by('d.id');
		$query = $this->db->get()->result();

		$summary = [];
		foreach ($query as $row) {
			$row->sisa_kuota = (int)$row->total_diminta - (int)$row->total_terisi;
			if($row->sisa_kuota < 0) $row->sisa_kuota = 0;

			// Hanya masukkan ke list jika divisi tersebut PERNAH atau SEDANG punya permintaan
			if($row->total_diminta > 0) {
				$summary[] = $row;
			}
		}
		return $summary;
	}
	public function get_performance_report() {
		$this->db->select('
			d.id, 
			d.nama_divisi,
			(SELECT COUNT(*) FROM users WHERE divisi_id = d.id AND role_id = 3) as jml_peserta,
			(SELECT COUNT(*) FROM absensi a JOIN users u ON u.id = a.user_id WHERE u.divisi_id = d.id AND a.status = "hadir") as total_hadir,
			(SELECT COUNT(*) FROM absensi a JOIN users u ON u.id = a.user_id WHERE u.divisi_id = d.id AND a.status = "telat") as total_telat
		');
		$this->db->from('master_divisi d');
		return $this->db->get()->result();
	}
}
