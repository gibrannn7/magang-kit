<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 15px; padding-bottom: 10px; }
        
        .title-section { text-align: center; margin-bottom: 15px; }
        .title-section h3 { text-decoration: underline; margin: 0; font-size: 14px; }
        
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 4px; }
        table.data-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        
        .sign-box { float: right; width: 200px; margin-top: 25px; text-align: center; }

        footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 90px;
            font-family: Arial, sans-serif;
            font-size: 7pt;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <?php
        $logo_path = FCPATH . 'assets/img/logo.png';
        $logo_data = (file_exists($logo_path)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path)) : '';
        
        $iso_path = FCPATH . 'assets/img/logoiso_02.png';
        $iso_data = (file_exists($iso_path)) ? 'data:image/png;base64,' . base64_encode(file_get_contents($iso_path)) : '';
    ?>

    <table class="header-table">
        <tr>
            <td width="300">
                <?php if($logo_data): ?>
                    <img src="<?= $logo_data ?>" width="200">
                <?php endif; ?>
            </td>
            <td></td>
        </tr>
    </table>

    <div class="title-section">
        <h3>REKAPITULASI KEHADIRAN PESERTA MAGANG</h3>
        <p style="margin: 3px;">Periode: <?= date('d/m/Y', strtotime($detail->tgl_mulai)) ?> s/d <?= date('d/m/Y', strtotime($detail->tgl_selesai)) ?></p>
    </div>

    <table class="info-table">
        <tr><td width="100">Nama</td><td>: <b><?= $peserta->nama_lengkap ?></b></td></tr>
        <tr><td>Instansi</td><td>: <?= $detail->institusi ?></td></tr>
        <tr><td>Unit Kerja</td><td>: Krakatau Information Technology</td></tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">Tanggal</th>
                <th>Datang</th>
                <th>Pulang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($absensi as $row): ?>
            <tr>
                <td align="center"><?= $no++ ?></td>
                <td align="center"><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                <td align="center"><?= $row->jam_datang ?: '--:--' ?></td>
                <td align="center"><?= $row->jam_pulang ?: '--:--' ?></td>
                <td align="center"><?= strtoupper($row->status) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($absensi)): ?>
                <tr><td colspan="5" align="center">Tidak ada data kehadiran.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="sign-box">
        Cilegon, <?= date('d F Y') ?><br>
        Pembimbing Lapangan,<br><br><br><br>
        <b>Nama Pejabat</b><br>
        Nip. Pejabat
    </div>

    <footer>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="35%">
                    <b>PT Krakatau Information Technology</b><br>
                    Krakatau Steel Building 9<sup>th</sup> Floor,<br>
                    Jl. Jend. Gatot Subroto Kav, 54<br>
                    Jakarta Selatan 12950<br>
                    <span style="color:#0000FF; font-weight:bold;">P</span> +62 21 - 5200732
                </td>
                
                <td width="35%" style="padding-left: 10px;">
                    <b>Cilegon Office</b><br>
                    Jl. Raya Anyer Km. 3 Cilegon 42441,<br>
                    Banten - Indonesia<br>
                    <span style="color:#0000FF; font-weight:bold;">P</span> +62 254 - 8317021<br>
                    www.krakatau-it.co.id
                </td>
                
                <td width="30%" align="right">
                    <?php if($iso_data): ?>
                        <img src="<?= $iso_data ?>" height="50">
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </footer>
</body>
</html>
