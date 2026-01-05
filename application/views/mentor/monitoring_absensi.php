<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">Monitoring Kehadiran Divisi</h3>
            <form action="<?= base_url('mentor/monitoring_absensi') ?>" method="get" class="form-inline">
                <input type="date" name="tanggal" class="form-control form-control-sm mr-2" value="<?= $tanggal ?>">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Nama Peserta</th>
                        <th>Jam Datang</th>
                        <th>Jam Pulang</th>
                        <th>Status</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($absensi)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada data absensi untuk tanggal ini.</td></tr>
                    <?php endif; ?>

                    <?php foreach($absensi as $row): ?>
                    <tr>
                        <td class="align-middle"><strong><?= $row->nama_lengkap ?></strong></td>
                        <td class="align-middle"><?= $row->jam_datang ?: '<span class="text-danger">--:--</span>' ?></td>
                        <td class="align-middle"><?= $row->jam_pulang ?: '<span class="text-danger">--:--</span>' ?></td>
                        <td class="align-middle">
                            <?php if($row->status == 'hadir'): ?>
                                <span class="badge badge-success">Hadir</span>
                            <?php elseif($row->status == 'telat'): ?>
                                <span class="badge badge-danger">Terlambat</span>
                            <?php elseif($row->status == 'izin'): ?>
                                <span class="badge badge-warning">Izin: <?= $row->jenis_izin ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Belum Absen</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('mentor/rekap_absensi/'.$row->user_id) ?>" class="btn btn-info btn-xs" target="_blank">
                                <i class="fas fa-file-pdf"></i> Rekap
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
