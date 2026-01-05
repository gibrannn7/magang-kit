<?php
$status_magang = $pendaftar->status;
?>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title text-bold">
                    <i class="fas fa-id-card-alt mr-1"></i> Informasi Penempatan Magang
                </h3>
            </div>
            <div class="card-body">
                <?php 
                    $u_id = $this->session->userdata('user_id');
                    $p_data = $this->db->select('d.nama_divisi, l.*')
                                       ->from('users u')
                                       ->join('master_divisi d', 'd.id = u.divisi_id', 'left')
                                       ->join('master_lokasi l', 'l.id = u.lokasi_id', 'left')
                                       ->where('u.id', $u_id)
                                       ->get()
                                       ->row();
                ?>
                <div class="row text-center">
                    <div class="col-sm-4 border-right">
                        <small class="text-muted d-block text-uppercase">Divisi</small>
                        <span class="text-lg text-bold text-primary">
                            <?= $p_data ? $p_data->nama_divisi : '-' ?>
                        </span>
                    </div>
                    <div class="col-sm-4 border-right">
                        <small class="text-muted d-block text-uppercase">Lokasi Ruangan</small>
                        <span class="text-lg text-bold">
                            <?= $p_data ? $p_data->nama_lokasi : '-' ?>
                        </span>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block text-uppercase">Titik Absen (Radius)</small>
                        <span class="badge badge-info">
                            <?= $p_data ? $p_data->radius_meter . ' Meter' : '-' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<?php if($lokasi_absen): ?>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title text-bold">
                    <i class="fas fa-map-marked-alt mr-1"></i> Area Geofencing Absensi Anda
                </h3>
            </div>
            <div class="card-body">
                <div id="map" style="height: 300px; border-radius: 10px;"></div>
                <div class="mt-2 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Pastikan Anda berada di dalam lingkaran biru untuk dapat melakukan absensi.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var officeLat = <?= $lokasi_absen->latitude ?>;
    var officeLong = <?= $lokasi_absen->longitude ?>;
    var radius = <?= $lokasi_absen->radius_meter ?>;

    var map = L.map('map').setView([officeLat, officeLong], 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([officeLat, officeLong]).addTo(map)
        .bindPopup('<b>Titik Pusat Absen</b><br><?= $lokasi_absen->nama_lokasi ?>')
        .openPopup();

    L.circle([officeLat, officeLong], {
        color: '#0099CC',
        fillColor: '#0099CC',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLat = position.coords.latitude;
            var userLong = position.coords.longitude;

            L.circleMarker([userLat, userLong], {
                radius: 8,
                fillColor: "#ff0000",
                color: "#fff",
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map).bindPopup("Posisi Anda Saat Ini");
        });
    }
});
</script>
<?php endif; ?>
<div class="row">
    <div class="col-md-12">
        <?php if($status_magang == 'selesai' && !empty($pendaftar->file_sertifikat)): ?>
        <div class="alert alert-success alert-dismissible">
            <h5><i class="icon fas fa-check"></i> Selamat!</h5>
            Program magang Anda telah selesai. Sertifikat resmi sudah diterbitkan oleh Admin.
            <br>
            <a href="<?= base_url('assets/uploads/sertifikat/' . $pendaftar->file_sertifikat) ?>" 
               target="_blank"
               class="btn btn-light btn-sm mt-2 text-success font-weight-bold">
               <i class="fas fa-download"></i> Download Sertifikat Resmi (PDF)
            </a>
        </div>
        <?php elseif($status_magang == 'selesai'): ?>
        <div class="alert alert-info">
            <h5><i class="icon fas fa-info-circle"></i> Info Sertifikat</h5>
            Status magang Anda telah <b>Selesai</b>. Mohon tunggu Admin melakukan validasi dan upload sertifikat resmi Anda.
        </div>
        <?php endif; ?>
    </div>

    <?php if(!empty($pendaftar->file_surat_balasan)): ?>
    <div class="col-md-12 mb-3">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-envelope-open-text"></i> Surat Balasan Magang Resmi</h5>
            Surat balasan resmi dari Krakatau Information Technology (E-Sign) telah tersedia. Silakan unduh sebagai bukti resmi penerimaan Anda.
            <br>
            <a href="<?= base_url('assets/uploads/surat_balasan/' . $pendaftar->file_surat_balasan) ?>" 
               target="_blank" 
               class="btn btn-light text-info font-weight-bold mt-2">
               <i class="fas fa-download"></i> Download Surat Balasan
            </a>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Kehadiran Hari Ini (<?= date('d M Y') ?>)</h3>
            </div>
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h5>ABSEN DATANG</h5>
                        <h1 class="font-weight-bold text-primary">
                            <?= isset($absensi->jam_datang) ? date('H:i', strtotime($absensi->jam_datang)) : '--:--' ?>
                        </h1>
                        <?php if(!isset($absensi->jam_datang)): ?>
                            <a href="<?= base_url('peserta/absen_area') ?>" class="btn btn-primary btn-block mt-3">Absen Sekarang</a>
                        <?php else: ?>
                            <span class="badge badge-success mt-2"><?= strtoupper($absensi->status) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5>ABSEN PULANG</h5>
                        <h1 class="font-weight-bold text-danger">
                            <?= isset($absensi->jam_pulang) ? date('H:i', strtotime($absensi->jam_pulang)) : '--:--' ?>
                        </h1>
                        <?php if(isset($absensi->jam_datang) && !isset($absensi->jam_pulang)): ?>
                            <a href="<?= base_url('peserta/absen_area') ?>" class="btn btn-danger btn-block mt-3">Absen Pulang</a>
                        <?php elseif(!isset($absensi->jam_datang)): ?>
                            <span class="text-muted">Absen datang terlebih dahulu</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">5 Riwayat Absensi Terakhir</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Datang</th>
                            <th>Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($riwayat)): foreach($riwayat as $r): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($r->tanggal)) ?></td>
                            <td><?= $r->jam_datang ?></td>
                            <td><?= $r->jam_pulang ?? '-' ?></td>
                            <td>
                                <span class="badge badge-success">
                                    <?= strtoupper($r->status) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada riwayat absensi
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
