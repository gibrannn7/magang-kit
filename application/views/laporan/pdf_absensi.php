<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; }
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; }
        .kop-table td { border: none; padding: 5px; }
        .logo-bps { width: 90px; }
        .kop-text { text-align: left; }
        .kop-title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .kop-sub { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .kop-address { font-size: 10px; font-style: italic; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #000; padding: 5px; }
        table.data th { background-color: #eee; text-align: center; }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td width="100" align="center">
                <?php
$logoPath = FCPATH . 'assets/img/logo.png';
$logoBase64 = base64_encode(file_get_contents($logoPath));
?>

<img src="data:image/png;base64,<?= $logoBase64 ?>" class="logo-bps">
            </td>
            <td class="kop-text">
                <span class="kop-title">KRAKATAU INFORMATION TECHNOLOGY</span><br>
                <span class="kop-address">
					Gedung Krakatau IT, Jl. Raya Anyer No.Km.3, Warnasari, Citangkil, Cilegon, Banten 42441.<br>
					Telp: (+62) 81110555605. Email: <span style="color: blue; text-decoration: underline;">email@@krakatau-it.co.id</span>, Website: https://www.krakatau-it.co.id/
				</span>
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="text-decoration: underline; margin: 0;">REKAPITULASI KEHADIRAN</h3>
        <p style="margin: 5px;">Periode: <?= $detail->tgl_mulai ?> s/d <?= $detail->tgl_selesai ?></p>
    </div>

    <table>
        <tr><td width="100">Nama</td><td>: <b><?= $peserta->nama_lengkap ?></b></td></tr>
        <tr><td>Instansi</td><td>: <?= $detail->institusi ?></td></tr>
        <tr><td>Unit Kerja</td><td>: Krakatau Information Technology</td></tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Datang</th>
                <th>Pulang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($absensi as $row): ?>
            <tr>
                <td align="center"><?= $no++ ?></td>
                <td align="center"><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                <td align="center"><?= $row->jam_datang ?></td>
                <td align="center"><?= $row->jam_pulang ?></td>
                <td align="center"><?= strtoupper($row->status) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="float: right; width: 200px; margin-top: 40px; text-align: center;">
        Cilegon, <?= date('d F Y') ?><br>
        Jabatan,<br><br><br><br>
        <b>Nama Pejabat</b><br>
        Nip. Pejabat
    </div>
</body>
</html>
