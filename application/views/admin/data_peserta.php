<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Database Seluruh Peserta Magang</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover datatable-init">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama & No. HP</th>
                        <th>Institusi/Fakultas</th>
                        <th>No. Surat / Tanggal</th>
                        <th>Periode</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; foreach ($list as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= $row->nama ?></strong><br>
                            <a href="https://wa.me/<?= $row->no_hp ?>" target="_blank" class="text-success">
                                <i class="fab fa-whatsapp"></i> <?= $row->no_hp ?>
                            </a>
                            <br>
                            <span class="badge badge-info"><?= ucfirst($row->jenis_peserta) ?></span>
                        </td>
                        <td>
                            <?= $row->institusi ?><br>
                            <small><b>Fak:</b> <?= $row->fakultas ?></small><br>
                            <small><b>Jur:</b> <?= $row->jurusan ?></small>
                        </td>
                        <td>
                            <small>No: <?= $row->no_surat ?></small><br>
                            <small>Tgl: <?= date('d/m/Y', strtotime($row->tgl_surat)) ?></small>
                        </td>
                        <td>
                            <?= date('d M Y', strtotime($row->tgl_mulai)) ?><br>
                            s/d<br>
                            <?= date('d M Y', strtotime($row->tgl_selesai)) ?>
                        </td>
                        <td>
                            <?php 
                                $color = 'secondary';
                                if($row->status == 'pending') $color = 'warning';
                                if($row->status == 'diterima') $color = 'primary';
                                if($row->status == 'ditolak') $color = 'danger';
                                if($row->status == 'selesai') $color = 'success';
                            ?>
                            <span class="badge badge-<?= $color ?>"><?= strtoupper($row->status) ?></span>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/berkas/'.$row->id) ?>" class="btn btn-xs btn-info btn-block">
                                <i class="fas fa-eye"></i> Detail
                            </a>

                            <?php if($row->user_id): ?>
                            <button 
                                type="button"
                                class="btn btn-xs btn-warning btn-block mt-1 btn-reset-pass"
                                data-url="<?= base_url('admin/reset_password/'.$row->user_id) ?>"
                                data-nama="<?= $row->nama ?>">
                                <i class="fas fa-key"></i> Reset Pass
                            </button>
                            <?php endif; ?>

                            <?php if($row->status == 'diterima' || $row->status == 'selesai'): ?>
                                <a href="<?= base_url('admin/rekap_absensi/'.$row->user_id) ?>" 
                                   target="_blank" 
                                   class="btn btn-xs btn-default btn-block mt-1">
                                    <i class="fas fa-file-pdf"></i> Absen
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-reset-pass')) {
        const btn = e.target.closest('.btn-reset-pass');
        const url = btn.getAttribute('data-url');
        const nama = btn.getAttribute('data-nama');

        Swal.fire({
            title: 'Reset Password?',
            html: `Password user <b>${nama}</b> akan direset menjadi:<br><b>123456</b>`,
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
