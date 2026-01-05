<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr class="bg-white">
                            <th colspan="2" class="text-center text-bold text-dark">
                                <i class="fas fa-user-graduate"></i> BIODATA PESERTA
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 30%;">Nama Lengkap</th>
                            <td><?= $pendaftar->nama ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= $pendaftar->email ?></td>
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
						<tr>
							<th>Divisi Penempatan (Final)</th>
							<td>
								<?php if($pendaftar->status == 'diterima'): ?>
									<?php 
										$user_fix = $this->db->get_where('users', ['id' => $pendaftar->user_id])->row();
										$div_fix = $this->db->get_where('master_divisi', ['id' => $user_fix->divisi_id])->row();
										echo '<span class="badge badge-success">'.$div_fix->nama_divisi.'</span>';
									?>
								<?php else: ?>
									<i class="text-muted small">Belum ditentukan</i>
								<?php endif; ?>
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
						<th>Status Pendaftaran</th>
						<td>
							<?php 
							$status_labels = [
								'pending' => 'badge-warning',
								'diterima' => 'badge-success',
								'ditolak' => 'badge-danger',
								'selesai' => 'badge-info'
							];
							$label = isset($status_labels[$pendaftar->status]) ? $status_labels[$pendaftar->status] : 'badge-secondary';
							?>
							<span class="badge <?= $label ?>"><?= strtoupper($pendaftar->status) ?></span>
						</td>
					</tr>

					<tr>
						<th>Minat Divisi (Pilihan Pendaftar)</th>
						<td>
							<?php 
								$minat = $this->db->get_where('master_divisi', ['id' => $pendaftar->divisi_id])->row(); 
								echo $minat ? '<strong class="text-primary">'.$minat->nama_divisi.'</strong>' : '<i class="text-muted">Tidak memilih</i>';
							?>
						</td>
					</tr>

					<?php if($pendaftar->status == 'pending'): ?>
						<tr class="bg-light">
							<th class="align-middle">Aksi Verifikasi</th>
							<td>
								<div class="d-flex">
									<button type="button" data-toggle="modal" data-target="#modalTerima" class="btn btn-success mr-2 font-weight-bold">
										<i class="fas fa-check-circle"></i> Terima Peserta
									</button>

									<button type="button" 
										onclick="confirmAction('<?= base_url('admin/verifikasi/'.$pendaftar->id.'/ditolak') ?>', 'tolak')"
										class="btn btn-danger font-weight-bold">
										<i class="fas fa-times-circle"></i> Tolak Peserta
									</button>
								</div>
								<small class="text-muted mt-2 d-block">*Anda akan menentukan penempatan divisi & lokasi setelah klik Terima.</small>
							</td>
						</tr>
					<?php endif; ?>

                        <tr class="bg-light">
							<th colspan="2" class="text-center text-bold">STATUS & BERKAS RESMI (E-SIGN)</th>
						</tr>

						<?php if(in_array($pendaftar->status, ['diterima', 'ditolak', 'selesai'])): ?>
						<tr>
							<th>Surat Balasan (E-Sign)</th>
							<td>
								<div class="mb-3">
									<?php if($pendaftar->file_surat_balasan): ?>
										<span class="badge badge-success mb-2"><i class="fas fa-check-circle"></i> File E-Sign Sudah Diupload</span><br>
										<a href="<?= base_url('assets/uploads/surat_balasan/'.$pendaftar->file_surat_balasan) ?>" target="_blank" class="btn btn-sm btn-info">
											<i class="fas fa-file-pdf"></i> Lihat File Final
										</a>
									<?php else: ?>
										<span class="text-danger d-block mb-1 small text-bold">Belum ada file E-Sign</span>
										<?php if($pendaftar->file_draft_balasan): ?>
											<a href="<?= base_url('assets/uploads/surat_balasan/'.$pendaftar->file_draft_balasan) ?>" target="_blank" class="btn btn-xs btn-outline-secondary">
												<i class="fas fa-download"></i> Download Draft (<?= strtoupper($pendaftar->status) ?>)
											</a>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<form id="form-upload-push" action="<?= base_url('admin/upload_file_final') ?>" method="post" enctype="multipart/form-data">
							<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
							<input type="hidden" name="id" value="<?= $pendaftar->id ?>">
							<input type="hidden" name="notif_method" id="notif_method">
							
							<div class="input-group input-group-sm">
								<input type="file" name="file_upload" id="input_file_esign" class="form-control" accept=".pdf" required>
								<div class="input-group-append">
									<button type="button" onclick="handleUploadPush()" class="btn btn-primary">
										<i class="fas fa-upload"></i> Upload & Push
									</button>
								</div>
							</div>
						</form>
							</td>
						</tr>
						<?php endif; ?>

                        <?php if($pendaftar->status == 'selesai'): ?>
                        <tr>
                            <th>Sertifikat Magang</th>
                            <td>
                                <div class="mb-3">
                                    <?php if($pendaftar->file_sertifikat): ?>
                                        <span class="badge badge-success mb-2"><i class="fas fa-check-circle"></i> Sertifikat Final Terkirim</span><br>
                                        <a href="<?= base_url('assets/uploads/sertifikat/'.$pendaftar->file_sertifikat) ?>" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-certificate"></i> Lihat Sertifikat Final
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('admin/cetak_sertifikat/'.$pendaftar->id) ?>" target="_blank" class="btn btn-xs btn-outline-secondary">
                                            <i class="fas fa-download"></i> Download Draft Sertifikat
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <form action="<?= base_url('admin/upload_file_final') ?>" method="post" enctype="multipart/form-data" class="form-inline">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                    <input type="hidden" name="id" value="<?= $pendaftar->id ?>">
                                    <input type="hidden" name="tipe" value="sertifikat">
                                    <div class="input-group input-group-sm">
                                        <input type="file" name="file_upload" class="form-control" accept=".pdf" required>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Push ke Peserta</button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php 
                        if(in_array($pendaftar->status, ['diterima', 'aktif', 'selesai']) && !empty($pendaftar->user_id) && isset($user_account)): 
                        ?>
                            <tr class="bg-white">
                                <th colspan="2" class="text-center text-bold text-dark">
                                    <i class="fas fa-key"></i> AKUN LOGIN PESERTA
                                </th>
                            </tr>
                            <tr>
                                <th>Email Login</th>
                                <td class="text-bold text-primary"><?= $user_account->email ?></td>
                            </tr>
                            <tr>
                                <th>Password Default</th>
                                <td>
                                    <span>123456</span>
                                    <br>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <tr class="bg-light">
                            <th colspan="2" class="text-center text-bold">DOKUMEN PENDAFTARAN (UPLOAD PESERTA)</th>
                        </tr>
                        <?php 
                        if(empty($dokumen)): ?>
                            <tr><td colspan="2" class="text-center">Tidak ada dokumen.</td></tr>
                        <?php else: foreach($dokumen as $doc): ?>
                            <tr>
                                <th><?= ucwords(str_replace('_', ' ', $doc->jenis_dokumen)) ?></th>
                                <td>
                                    <?php 
                                    // FIX BUG: Folder path disesuaikan dengan struktur uploads/
                                    $sub_folder = ($doc->jenis_dokumen == 'foto') ? 'foto/' : (($doc->jenis_dokumen == 'cv') ? 'cv/' : 'surat/');
                                    $file_path = FCPATH . 'assets/uploads/' . $sub_folder . $doc->file_path;
                                    ?>
                                    <?php if(file_exists($file_path)): ?>
                                        <a href="<?= base_url('assets/uploads/'.$sub_folder.$doc->file_path) ?>" target="_blank" class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye"></i> Lihat File
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-danger">File tidak ditemukan</span>
                                    <?php endif; ?>
                                    <div class="text-muted text-xs mt-1"><?= $doc->file_name_original ?></div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalTerima" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-check"></i> Konfirmasi Penerimaan & Penempatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/verifikasi/'.$pendaftar->id.'/diterima') ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> Peserta akan mendapatkan akun login otomatis setelah data ini disimpan.</small>
                    </div>
					<div class="callout callout-info bg-light border-left-info shadow-sm">
						<h6 class="font-weight-bold text-info"><i class="fas fa-users-cog mr-2"></i> Akumulasi Kebutuhan Divisi</h6>
						<table class="table table-sm table-striped mb-0 mt-2" style="font-size: 13px;">
							<thead>
								<tr class="text-muted">
									<th>Divisi</th>
									<th class="text-center">Kebutuhan</th>
									<th class="text-center">Sisa Slot</th>
								</tr>
							</thead>
							<tbody>
							<?php if(empty($kebutuhan_divisi)): ?>
								<tr><td colspan="3" class="text-center text-muted">Belum ada permintaan dari divisi manapun.</td></tr>
							<?php endif; ?>

							<?php foreach($kebutuhan_divisi as $kb): ?>
								<tr>
									<td class="align-middle"><?= $kb->nama_divisi ?></td>
									<td class="text-center align-middle"><?= $kb->total_terisi ?> / <?= $kb->total_diminta ?></td>
									<td class="text-center">
										<?php if($kb->sisa_kuota > 0): ?>
											<span class="badge badge-danger"><?= $kb->sisa_kuota ?> Slot</span>
										<?php else: ?>
											<span class="badge badge-secondary">Terpenuhi</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<hr>                   
                    <div class="form-group">
                        <label class="font-weight-bold">Divisi Penempatan Final</label>
                        <select name="divisi_id_final" class="form-control" required>
                            <option value="">-- Pilih Divisi --</option>
                            <?php foreach($divisi_list as $dl): ?>
                                <option value="<?= $dl->id ?>" <?= ($dl->id == $pendaftar->divisi_id) ? 'selected' : '' ?>>
                                    <?= $dl->nama_divisi ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Secara default memilih divisi pilihan peserta.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi Absensi (Geofencing)</label>
                        <select name="lokasi_id_final" class="form-control" required>
                            <option value="">-- Pilih Titik Lokasi --</option>
                            <?php foreach($lokasi_list as $ll): ?>
                                <option value="<?= $ll->id ?>">
                                    <?= $ll->nama_lokasi ?> (Radius: <?= $ll->radius_meter ?>m)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted text-danger">*Wajib diisi agar peserta bisa melakukan absensi.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success shadow">Simpan & Terima Peserta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function handleUploadPush() {
    const file = document.getElementById('input_file_esign').value;
    if (!file) {
        Swal.fire('Error', 'Silakan pilih file PDF E-Sign!', 'error');
        return;
    }

    Swal.fire({
        title: 'Kirim Notifikasi?',
        text: 'Pilih metode untuk mengirim surat balasan ke peserta:',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonColor: '#28a745', // Hijau (WA)
        denyButtonColor: '#007bff',    // Biru (Email)
        cancelButtonColor: '#d33',     // Merah (Batal)
        confirmButtonText: '<i class="fab fa-whatsapp"></i> WhatsApp',
        denyButtonText: '<i class="fas fa-envelope"></i> Email',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('notif_method').value = 'wa';
            document.getElementById('form-upload-push').submit();
        } else if (result.isDenied) {
            document.getElementById('notif_method').value = 'email';
            document.getElementById('form-upload-push').submit();
        }
    });
}

function confirmAction(url, action) {
    const isAccept = action === 'terima';
    
    Swal.fire({
        title: isAccept ? 'Terima Peserta?' : 'Tolak Peserta?',
        html: isAccept 
            ? 'Yakin ingin <b>menerima</b> peserta ini? Akun login akan dibuat secara otomatis.' 
            : 'Yakin ingin <b>menolak</b> peserta ini? Status akan berubah menjadi ditolak.',
        icon: isAccept ? 'success' : 'error',
        showCancelButton: true,
        confirmButtonColor: isAccept ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: isAccept ? 'Ya, Terima Peserta' : 'Ya, Tolak Peserta',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Pastikan URL bersih dari suffix notifikasi lama jika ada
            window.location.href = url.replace('/wa', '').replace('/email', '');
        }
    });
}
</script>
