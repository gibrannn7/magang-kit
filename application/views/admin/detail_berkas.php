<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <tbody>
				<tr class="bg-white">
                    <th colspan="2" class="text-center text-bold text-dark">
                        <i></i> BIODATA PESERTA
                    </th>
                </tr>
                <tr>
                    <th style="width: 30%;">Nama Lengkap</th>
                    <td><?= $pendaftar->nama ?></td>
                </tr>
                <tr>
                    <th>NIM / NIS</th>
                    <td><?= $pendaftar->nim_nis ?></td>
                </tr>
                <tr>
                    <th>Jenis Peserta</th>
                    <td><?= ucfirst($pendaftar->jenis_peserta) ?></td>
                </tr>
                <tr>
                    <th>Institusi / Sekolah</th>
                    <td><?= $pendaftar->institusi ?></td>
                </tr>
                <tr>
                    <th>Jurusan</th>
                    <td><?= $pendaftar->jurusan ?></td>
                </tr>
                <tr>
                    <th>Nomor WhatsApp</th>
                    <td>
                        <a href="https://wa.me/<?= $pendaftar->no_hp ?>" target="_blank" class="btn btn-success btn-xs">
                            <i class="fab fa-whatsapp"></i> <?= $pendaftar->no_hp ?>
                        </a>
                    </td>
                </tr>

                <tr class="bg-light">
                    <th colspan="2" class="text-center text-bold">DETAIL MAGANG</th>
                </tr>
                <tr>
                    <th>Jenis Magang</th>
                    <td><?= ucfirst($pendaftar->jenis_magang) ?></td>
                </tr>
                <tr>
                    <th>Periode Magang</th>
                    <td>
                        <?php 
                        // Format Tanggal Indonesia Sederhana
                        $tgl_mulai = date('d-m-Y', strtotime($pendaftar->tgl_mulai));
                        $tgl_selesai = date('d-m-Y', strtotime($pendaftar->tgl_selesai));
                        ?>
                        <?= $tgl_mulai ?> <span class="text-muted mx-1">s/d</span> <?= $tgl_selesai ?>
                        <br>
                        <small class="text-muted">(Durasi: <?= $pendaftar->durasi_bulan ?> Bulan)</small>
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php 
                        $status = $pendaftar->status;
                        $badge_color = 'secondary';
                        $status_label = ucfirst($status);

                        switch ($status) {
                            case 'pending':
                                $badge_color = 'warning';
                                $status_label = 'Menunggu Verifikasi';
                                break;
                            case 'diterima':
                                $badge_color = 'info';
                                break;
                            case 'aktif':
                                $badge_color = 'success';
                                $status_label = 'Aktif Magang';
                                break;
                            case 'ditolak':
                                $badge_color = 'danger';
                                break;
                            case 'selesai':
                                $badge_color = 'primary';
                                $status_label = 'Selesai Magang';
                                break;
                        }
                        ?>
                        <span class="badge badge-<?= $badge_color ?> text-md p-2">
                            <?= $status_label ?>
                        </span>
                    </td>
                </tr>

                <?php 
                if(in_array($pendaftar->status, ['diterima', 'aktif', 'selesai']) && !empty($pendaftar->user_id)): 
                    // Ambil data user langsung dari view agar tidak perlu ubah controller
                    $user_account = $this->db->get_where('users', ['id' => $pendaftar->user_id])->row();
                    if($user_account):
                ?>
                <tr class="bg-white">
                    <th colspan="2" class="text-center text-bold text-dark">
                        <i class="fas fa-key"></i> AKUN LOGIN PESERTA
                    </th>
                </tr>
                <tr>
                    <th>Username</th>
                    <td class="text-bold text-primary"><?= $user_account->username ?></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td>
                        <span>123456</span>
                        <br>
                    </td>
                </tr>
                <?php 
                    endif; 
                endif; 
                ?>

                <tr class="bg-light">
                    <th colspan="2" class="text-center text-bold">BERKAS DOKUMEN</th>
                </tr>
                <?php 
                $docs = $this->db->get_where('dokumen', ['pendaftar_id' => $pendaftar->id])->result();
                if(empty($docs)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted">Tidak ada dokumen diupload</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($docs as $doc): ?>
                    <tr>
                        <th>
                            <?= ucwords(str_replace('_', ' ', $doc->jenis_dokumen)) ?>
                        </th>
                        <td>
                            <?php 
                            // Tentukan folder berdasarkan jenis dokumen
                            $folder = 'surat/';
                            if($doc->jenis_dokumen == 'foto') $folder = 'foto/';
                            if($doc->jenis_dokumen == 'cv') $folder = 'cv/';
                            
                            $file_url = base_url('assets/uploads/' . $folder . $doc->file_path);
                            $file_path = FCPATH . 'assets/uploads/' . $folder . $doc->file_path;
                            ?>

                            <?php if(file_exists($file_path)): ?>
                                <a href="<?= $file_url ?>" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Lihat File
                                </a>
                            <?php else: ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> File tidak ditemukan</span>
                            <?php endif; ?>
                            
                            <div class="text-muted text-xs mt-1">
                                <?= $doc->file_name_original ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>
