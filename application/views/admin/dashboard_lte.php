<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3><?= $total_daftar ?></h3><p>Total Pendaftar</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3><?= $pending ?></h3><p>Perlu Verifikasi</p></div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3><?= $aktif ?></h3><p>Sedang Magang</p></div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-indigo">
            <div class="inner"><h3><?= $selesai ?></h3><p>Alumni / Selesai</p></div>
            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
        </div>
    </div>
</div>

<h5 class="mb-3 mt-4"><i class="fas fa-calendar-alt mr-2"></i> Kehadiran Peserta Hari Ini (<?= date('d M Y') ?>)</h5>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3><?= $hadir ?></h3><p>Tepat Waktu</p></div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="<?= base_url('admin/monitoring_absensi') ?>" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner"><h3><?= $telat ?></h3><p>Terlambat</p></div>
            <div class="icon"><i class="fas fa-history"></i></div>
            <a href="<?= base_url('admin/monitoring_absensi') ?>" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3><?= $absen_izin ?></h3><p>Izin / Sakit</p></div>
            <div class="icon"><i class="fas fa-envelope-open-text"></i></div>
            <a href="<?= base_url('admin/monitoring_absensi?status=izin') ?>" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner"><h3><?= $belum_absen ?></h3><p>Belum Absen</p></div>
            <div class="icon"><i class="fas fa-user-slash"></i></div>
            <a href="<?= base_url('admin/monitoring_absensi?status=belum') ?>" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#tab-pending">Verifikasi (<?= $pending ?>)</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-aktif">Aktif Magang (<?= $aktif ?>)</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-selesai">Arsip/Selesai</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-pending">
                <table class="table table-bordered table-striped datatable-init">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendaftar as $p): if($p->status != 'pending') continue; ?>
                        <tr>
                            <td>
                                <?= $p->nama ?><br>
                                <small class="text-muted"><?= $p->jurusan ?></small>
                            </td>
                            <td><?= $p->institusi ?></td>
                            <td>
								<a href="<?= base_url('admin/berkas/'.$p->id) ?>" class="btn btn-xs btn-info" title="Lihat Detail">
									<i class="fas fa-eye"></i> Detail
								</a>
								<button type="button"
										onclick="openModalTerima(<?= $p->id ?>, '<?= addslashes($p->nama) ?>', '<?= $p->divisi_id ?>')"
										class="btn btn-xs btn-success"
										title="Terima">
									<i class="fas fa-check"></i> Terima
								</button>

								<button type="button"
										onclick="confirmAction('<?= base_url('admin/verifikasi/'.$p->id.'/ditolak') ?>', 'tolak')"
										class="btn btn-xs btn-danger"
										title="Tolak">
									<i class="fas fa-times"></i> Tolak
								</button>
							</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="tab-aktif">
                <table class="table table-bordered table-striped datatable-init">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendaftar as $p): if($p->status != 'diterima') continue; ?>
                        <tr>
                            <td>
                                <strong><?= $p->nama ?></strong>
                                <?php if($p->akun_user): ?>
                                    <br><small class="text-success"><i class="fas fa-user-check"></i> Akun Aktif</small>
                                <?php else: ?>
                                    <br><small class="text-danger"><i class="fas fa-exclamation-circle"></i> Error Akun</small>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/y', strtotime($p->tgl_mulai)) ?> - <?= date('d/m/y', strtotime($p->tgl_selesai)) ?></td>
                            <td>
                                <a href="<?= base_url('admin/berkas/'.$p->id) ?>" class="btn btn-xs btn-info" title="Lihat Detail & Akun"><i class="fas fa-eye"></i> Detail</a>

                                <a href="<?= base_url('admin/rekap_absensi/'.$p->user_id) ?>" target="_blank" class="btn btn-xs btn-default border" title="Rekap Absensi">
                                    <i class="fas fa-file-pdf text-danger"></i> Rekap
                                </a>

                                <?php if($p->user_id): ?>
                                <button 
                                    type="button"
                                    class="btn btn-xs btn-warning btn-reset-pass"
                                    data-url="<?= base_url('admin/reset_password/'.$p->user_id) ?>"
                                    data-nama="<?= $p->nama ?>"
                                    title="Reset Password ke 123456">
                                    <i class="fas fa-key"></i> Reset
                                </button>
                                <?php endif; ?>
                                <button type="button"
                                    onclick="confirmTamatkan('<?= base_url('admin/set_selesai/'.$p->id) ?>')"
                                    class="btn btn-xs btn-primary font-weight-bold"
                                    title="Tamatkan">
                                    <i class="fas fa-flag-checkered"></i> Tamat
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="tab-selesai">
                 <table class="table table-bordered datatable-init">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendaftar as $p): if($p->status != 'selesai' && $p->status != 'ditolak') continue; ?>
                        <tr>
                            <td><?= $p->nama ?></td>
                            <td><?= $p->institusi ?></td>
                            <td>
                                <span class="badge badge-<?= $p->status == 'selesai' ? 'success' : 'danger' ?>"><?= strtoupper($p->status) ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/berkas/'.$p->id) ?>" class="btn btn-xs btn-info" title="Lihat Detail"><i class="fas fa-eye"></i> Detail</a>

                                <?php if($p->status == 'selesai'): ?>
                                    <a href="<?= base_url('admin/rekap_absensi/'.$p->user_id) ?>" target="_blank" class="btn btn-xs btn-default border" title="Rekap Akhir">
                                        <i class="fas fa-file-pdf text-danger"></i> Rekap
                                    </a>
                                    
                                    <?php if($p->user_id): ?>
										<button 
											type="button"
											class="btn btn-xs btn-warning btn-reset-pass"
											data-url="<?= base_url('admin/reset_password/'.$p->user_id) ?>"
											data-nama="<?= $p->nama ?>"
											title="Reset Password">
											<i class="fas fa-key"></i> Reset
										</button>
									<?php endif; ?>
                            	<?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmAction(url, action) {
    const isAccept = action === 'terima';
    
    Swal.fire({
        title: isAccept ? 'Konfirmasi Terima Peserta?' : 'Konfirmasi Tolak Peserta?',
        html: isAccept 
            ? 'Peserta akan dinyatakan <b>DITERIMA</b>.<br>Sistem akan membuat akun login otomatis dan menyiapkan draft surat balasan.' 
            : 'Peserta akan dinyatakan <b>DITOLAK</b>.<br>Sistem akan menyiapkan draft surat penolakan resmi.',
        icon: isAccept ? 'success' : 'warning',
        showCancelButton: true,
        confirmButtonColor: isAccept ? '#28a745' : '#dc3545', // Hijau vs Merah
        cancelButtonColor: '#6c757d',
        confirmButtonText: isAccept ? '<i class="fas fa-check"></i> Ya, Terima!' : '<i class="fas fa-times"></i> Ya, Tolak!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
function confirmTamatkan(url) {
    Swal.fire({
        title: 'Tamatkan Magang?',
        text: 'Status peserta akan dipindahkan ke arsip / selesai.',
        icon: 'warning',
		iconColor: '#1261f3',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tamatkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#1261f3ff'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-reset-pass')) {
        const btn = e.target.closest('.btn-reset-pass');
        const url = btn.getAttribute('data-url');
        const nama = btn.getAttribute('data-nama');

        Swal.fire({
            title: 'Reset Password?',
            html: `Password user <b>${nama}</b> akan direset menjadi:<br><b style="font-size:1.2em">123456</b>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
});
</script>
<div class="modal fade" id="modalTerima" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-check"></i> Konfirmasi Penerimaan & Penempatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTerima" action="" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> Menyetujui <b id="nama_peserta_modal"></b>. Peserta akan mendapatkan akun login otomatis.</small>
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
                                <tr><td colspan="3" class="text-center text-muted">Belum ada data kebutuhan.</td></tr>
                            <?php else: foreach($kebutuhan_divisi as $kb): ?>
                                <tr>
                                    <td><?= $kb->nama_divisi ?></td>
                                    <td class="text-center"><?= $kb->total_terisi ?> / <?= $kb->total_diminta ?></td>
                                    <td class="text-center">
                                        <?php if($kb->sisa_kuota > 0): ?>
                                            <span class="badge badge-danger"><?= $kb->sisa_kuota ?> Slot</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Full</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>                   
                    <div class="form-group">
                        <label class="font-weight-bold">Divisi Penempatan Final</label>
                        <select name="divisi_id_final" id="divisi_id_final" class="form-control" required>
                            <option value="">-- Pilih Divisi --</option>
                            <?php foreach($divisi as $dl): ?>
                                <option value="<?= $dl->id ?>"><?= $dl->nama_divisi ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Lokasi Absensi (Geofencing)</label>
                        <select name="lokasi_id_final" class="form-control" required>
                            <option value="">-- Pilih Titik Lokasi --</option>
                            <?php foreach($lokasi as $ll): ?>
                                <option value="<?= $ll->id ?>">
                                    <?= $ll->nama_lokasi ?> (Radius: <?= $ll->radius_meter ?>m)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted text-danger">*Wajib agar peserta bisa absen.</small>
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
/**
 * Fungsi untuk membuka modal terima pendaftar
 * @param id ID Pendaftar
 * @param nama Nama Pendaftar
 * @param divisiId Pilihan divisi saat daftar
 */
function openModalTerima(id, nama, divisiId) {
    const url = "<?= base_url('admin/verifikasi/') ?>" + id + "/diterima";
    $('#formTerima').attr('action', url);
    $('#nama_peserta_modal').text(nama);
    $('#divisi_id_final').val(divisiId);
    $('#modalTerima').modal('show');
}
</script>
