<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Buat Permintaan Baru</h3></div>
            <form action="<?= base_url('mentor/add_request') ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="card-body">
                    <div class="form-group">
                        <label>Divisi</label>
                        <input type="text" class="form-control" value="<?= $nama_divisi ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Anak Magang Dibutuhkan</label>
                        <input type="number" name="jumlah" class="form-control" required min="1">
                    </div>
                    <div class="form-group">
                        <label>Keterangan / Kriteria</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Butuh 2 orang untuk bantu input data ERP"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block shadow-sm">Kirim Request ke HC</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Riwayat Permintaan Divisi Anda</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead><tr><th>Tgl Request</th><th>Jumlah</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach($requests as $r): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($r->created_at)) ?></td>
                            <td><span class="badge badge-info"><?= $r->jumlah ?> Orang</span></td>
                            <td><?= $r->keterangan ?></td>
                            <td>
                                <a href="<?= base_url('mentor/delete_request/'.$r->id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
