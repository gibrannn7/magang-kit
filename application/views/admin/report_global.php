<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title text-bold"><i class="fas fa-chart-pie mr-1"></i> Rekapitulasi Performa & Kebutuhan Divisi</h3>
        <div class="card-tools">
            <button onclick="window.print()" class="btn btn-default btn-sm"><i class="fas fa-print"></i> Cetak Laporan</button>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Nama Divisi</th>
                    <th class="text-center">Peserta Aktif</th>
                    <th class="text-center">Total Kehadiran</th>
                    <th class="text-center">Tingkat Kedisiplinan</th>
                    <th class="text-center">Status Pemenuhan Slot</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($report as $r): 
                    // Ambil data summary berdasarkan ID divisi baris ini
                    $kebutuhan = isset($summary_mapped[$r->id]) ? $summary_mapped[$r->id] : null;
                    
                    $total_absen = $r->total_hadir + $r->total_telat;
                    $disiplin = ($total_absen > 0) ? round(($r->total_hadir / $total_absen) * 100) : 0;
                ?>
                <tr>
                    <td><strong><?= $r->nama_divisi ?></strong></td>
                    <td class="text-center"><?= $r->jml_peserta ?> Orang</td>
                    <td class="text-center">
                        <span class="text-success"><?= $r->total_hadir ?> Hadir</span> / 
                        <span class="text-danger"><?= $r->total_telat ?> Telat</span>
                    </td>
                    <td class="text-center" style="width: 200px;">
                        <div class="progress progress-xs">
                            <div class="progress-bar bg-<?= ($disiplin > 80) ? 'success' : ($disiplin > 50 ? 'warning' : 'danger') ?>" style="width: <?= $disiplin ?>%"></div>
                        </div>
                        <small class="text-bold"><?= $disiplin ?>% Tepat Waktu</small>
                    </td>
                    <td class="text-center">
                        <?php if($kebutuhan): ?>
                            <?php if($kebutuhan->sisa_kuota == 0): ?>
                                <span class="badge badge-success px-2">Lengkap (<?= $kebutuhan->total_terisi ?>/<?= $kebutuhan->total_diminta ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-warning px-2">Sisa <?= $kebutuhan->sisa_kuota ?> Slot</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted small">No Request</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
