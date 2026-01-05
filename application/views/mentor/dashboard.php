<div class="row">
    <div class="col-lg-4 col-6 d-flex align-items-stretch">
        <div class="small-box bg-info w-100 shadow-sm">
            <div class="inner">
                <h3><?= $total_magang ?></h3>
                <p>
                    Total Anak Magang di Divisi<br>
                    <strong><?= $nama_divisi ?></strong>
                </p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>

    <div class="col-lg-4 col-6 d-flex align-items-stretch">
        <div class="small-box bg-success w-100 shadow-sm">
            <div class="inner">
                <h3><?= $hadir_today ?></h3>
                <p>Hadir Hari Ini</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
        </div>
    </div>

    <div class="col-lg-4 col-6 d-flex align-items-stretch">
        <div class="small-box bg-warning w-100 shadow-sm">
            <div class="inner text-white">
                <h3><?= ($my_summary) ? $my_summary->sisa_kuota : 0 ?></h3>
                <p>
                    Sisa Kuota Permintaan<br>
                    <strong>Yang Belum Terisi</strong>
                </p>
            </div>
            <div class="icon"><i class="fas fa-clipboard-list"></i></div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-3">
    <div class="card-header border-0">
        <h3 class="card-title text-bold">Daftar Anak Magang Penempatan Anda</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 datatable-init">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Peserta</th>
                        <th>Institusi</th>
                        <th>Email Akun</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($peserta as $p): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= $p->nama ?></strong></td>
                        <td><?= $p->institusi ?></td>
                        <td><code class="text-primary"><?= $p->email ?></code></td>
                        <td class="text-center">
                            <a href="<?= base_url('mentor/rekap_absensi/'.$p->user_id) ?>"
                               class="btn btn-primary btn-xs"
                               target="_blank">
                                <i class="fas fa-file-pdf"></i> Rekap Absen
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
