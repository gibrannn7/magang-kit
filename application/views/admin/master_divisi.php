<div class="row">
    <div class="col-md-5">
        <div class="card card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Divisi</h3>
            </div>

            <form action="<?= base_url('admin/master_divisi_add') ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Divisi</label>
                        <input type="text" name="nama_divisi" class="form-control" required placeholder="Contoh: Digital Transformation Division">
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Divisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Daftar Divisi</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped datatable-init">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Divisi</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($list as $l): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= $l->nama_divisi ?></strong></td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/master_divisi_delete/'.$l->id) ?>"
                                       onclick="return confirm('Hapus data divisi ini?')"
                                       class="btn btn-xs btn-danger shadow-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Data divisi belum tersedia</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
