<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Tambah Admin Baru</h3>
    </div>
    <?= form_open('admin/admin_add') ?>
        <div class="card-body">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama Admin" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email Admin" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-block">Simpan Admin</button>
        </div>
    <?= form_close() ?>
</div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Admin Sistem</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Nama</th><th>Email</th><th width="100">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach($admins as $a): ?>
                        <tr>
                            <td><?= $a->nama_lengkap ?></td>
                            <td><?= $a->email ?></td>
                            <td>
                                <a href="<?= base_url('admin/admin_delete/'.$a->id) ?>" 
                                   class="btn btn-danger btn-xs" onclick="return confirm('Hapus admin ini?')">
                                   <i class="fas fa-trash"></i> Hapus
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
