<div class="row">
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Tambah Staff Baru</h3></div>
            <?= form_open('admin/admin_add') ?>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role Account</label>
                        <select name="role" class="form-control" id="role_select" required>
                            <option value="admin">Admin HC (Full Access)</option>
                            <option value="mentor">Mentor Divisi (Monitoring)</option>
                        </select>
                    </div>
                    <div class="form-group" id="divisi_field" style="display:none;">
                        <label>Penempatan Divisi Mentor</label>
                        <select name="divisi_id" class="form-control">
                            <?php foreach($divisi as $d): ?>
                                <option value="<?= $d->id ?>"><?= $d->nama_divisi ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Simpan Akun</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Akun Staff (HC & Mentor)</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Nama</th><th>Role</th><th>Divisi</th><th width="80">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach($admins as $a): ?>
                        <tr>
                            <td><?= $a->nama_lengkap ?><br><small><?= $a->email ?></small></td>
                            <td><span class="badge <?= $a->role == 'admin' ? 'badge-danger' : 'badge-info' ?>"><?= strtoupper($a->role) ?></span></td>
                            <td><?= $a->nama_divisi ?: '-' ?></td>
                            <td>
                                <a href="<?= base_url('admin/admin_delete/'.$a->id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
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
document.getElementById('role_select').addEventListener('change', function() {
    document.getElementById('divisi_field').style.display = (this.value == 'mentor') ? 'block' : 'none';
});
</script>
