<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Fakultas</h3>
            </div>

            <form action="<?= base_url('admin/master_fakultas_add') ?>" method="POST">
                <input type="hidden"
                       name="<?= $this->security->get_csrf_token_name(); ?>"
                       value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Fakultas</label>
                        <input type="text"
                               name="nama_fakultas"
                               class="form-control"
                               required
                               placeholder="Contoh: Fakultas Teknik">
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered datatable-init">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Fakultas</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($list as $l): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $l->nama_fakultas ?></td>
                                <td>
                                    <a href="<?= base_url('admin/master_fakultas_delete/'.$l->id) ?>"
                                       onclick="return confirm('Hapus data fakultas ini?')"
                                       class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
