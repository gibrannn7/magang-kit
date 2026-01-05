<div class="row">
    <div class="col-md-5">
        <div class="card card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title text-bold">Tambah Data Lokasi</h3>
            </div>

            <form action="<?= base_url('admin/master_lokasi_add') ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Lokasi / Ruangan</label>
                        <input type="text" name="nama_lokasi" class="form-control" required placeholder="Contoh: Gedung HC Lantai 2">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Latitude</label>
                                <input type="text" name="latitude" class="form-control" required placeholder="-6.012345">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="longitude" class="form-control" required placeholder="106.123456">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Radius Toleransi (Meter)</label>
                        <input type="number" name="radius_meter" class="form-control" value="50" required>
                        <small class="text-muted">* Jarak maksimal user dari titik pusat saat absen.</small>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-map-marker-alt"></i> Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title text-bold">Daftar Titik Lokasi</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped datatable-init">
                    <thead>
                        <tr>
                            <th>Nama Lokasi</th>
                            <th class="text-center">Radius</th>
                            <th width="80" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $l): ?>
                            <tr>
                                <td>
                                    <strong><?= $l->nama_lokasi ?></strong><br>
                                    <small class="text-primary"><?= $l->latitude ?>, <?= $l->longitude ?></small>
                                </td>
                                <td class="text-center"><?= $l->radius_meter ?>m</td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/master_lokasi_delete/'.$l->id) ?>"
                                       onclick="return confirm('Hapus data lokasi ini?')"
                                       class="btn btn-xs btn-danger shadow-sm">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($list)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Data lokasi belum tersedia</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
