<!DOCTYPE html>
<html>
<head>
    <title>Surat Jawaban Permohonan PKL</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; color: #000; margin: 0; padding: 0; }
        .header-table { width: 100%; margin-bottom: 15px; padding-bottom: 5px; }
        
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { vertical-align: top; }

        .content-body { padding: 0 5px; text-align: justify; }
        
        .student-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .student-table th, .student-table td { border: 1px solid #000; padding: 5px; text-align: center; font-size: 10pt; }
        .student-table th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; }
        
        .rules-list {
			margin-left: 20px; 
			margin-top: 10px;
			padding-left: 20px; 
		}
        .rules-list li { margin-bottom: 5px; }

        .footer-sign-wrap { 
            width: 100%; 
            margin-top: 20px; 
            position: relative;
        }

        .tembusan-left { 
            float: left; 
            width: 55%; 
            font-size: 9pt; 
            margin-top: 60px;
        }

        .tembusan-left ol { 
            margin-left: -20px; 
            margin-top: 0; 
        }

        .ttd-right { 
            float: right; 
            width: 250px; 
            text-align: left; 
            font-size: 11pt; 
        }

        footer {
            position: fixed; bottom: -30px; left: 0; right: 0; height: 100px;
            font-family: Arial, sans-serif; font-size: 7pt; padding-top: 5px;
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
                    <img src="<?= $logo_data ?>" width="220">
                <?php endif; ?>
            </td>
            <td></td>
        </tr>
    </table>

    <div class="content-body">
        <table class="meta-table">
            <tr>
                <td width="50">No.</td>
                <td width="10">:</td>
                <td width="250"><?= rand(100,999) ?>/DIV.HC&SM/KIT/<?= date('m') ?>/<?= date('Y') ?></td>
                <td align="right">Cilegon, <?= date('d F Y') ?></td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td><b>Surat Jawaban Permohonan Kerja Praktek</b></td>
                <td></td>
            </tr>
            <tr>
                <td>Lamp</td>
                <td>:</td>
                <td><?= ($pendaftar->status == 'diterima') ? '1 Lembar' : '-' ?></td>
                <td></td>
            </tr>
        </table>

        <p>
            Yth. Dekan/Kepala/Pimpinan<br>
            <b><?= $pendaftar->institusi ?></b><br>
            di<br>
            Tempat
        </p>

        <p>Dengan hormat,</p>

        <p>
            Menanggapi Surat dari <?= $pendaftar->institusi ?> No. <?= $pendaftar->no_surat ?> Tanggal <?= date('d F Y', strtotime($pendaftar->tgl_surat)) ?>, perihal Permohonan PKL atas nama sebagai berikut:
        </p>

        <table class="student-table">
            <thead>
                <tr>
                    <th width="30">No.</th>
                    <th>Nama Siswa/i</th>
                    <th>NIM/NIS</th>
                    <th>Prodi/Jurusan</th>
                    <th>Lokasi Penempatan</th>
                    <th>Periode</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1.</td>
                    <td align="left"><b><?= strtoupper($pendaftar->nama) ?></b></td>
                    <td><?= $pendaftar->nim_nis ?></td>
                    <td><?= $pendaftar->jurusan ?></td>
                    <td><?= ($pendaftar->status == 'diterima') ? $nama_lokasi : '-' ?></td>
                    <td><?= date('d/m/y', strtotime($pendaftar->tgl_mulai)) ?> s.d <?= date('d/m/y', strtotime($pendaftar->tgl_selesai)) ?></td>
                </tr>
            </tbody>
        </table>

        <?php if($pendaftar->status == 'diterima'): ?>
            <p>Maka dengan ini kami <b>menyetujui</b> Permohonan PKL tersebut di PT Krakatau Information Technology.</p>
            
            <p><b>Hal yang harus diperhatikan dalam pelaksanaan adalah sebagai berikut:</b></p>
            <ol class="rules-list">
                <li>Melaksanakan Program PKL dengan tertib dan mematuhi aturan yang berlaku di Perusahaan.</li>
                <li>Melaksanakan Program PKL di tempat yang sudah ditentukan (Divisi: <b><?= $nama_divisi ?></b>).</li>
                <li>Menyerahkan Pas Photo 4x6: 1 Lembar (Tulis Nama & NIM) dikirimkan melalui email Human Capital.</li>
                <li>Membuat absen kehadiran melalui sistem dan laporan akhir praktek kerja lapangan.</li>
                <li>Dilarang melakukan aktifitas tanpa petunjuk dari pembimbing lapangan.</li>
                <li>Dilarang melakukan copy/backup dokumen perusahaan termasuk source code program tanpa izin.</li>
            </ol>
            <p>Demikian kami sampaikan atas perhatian dan kerjasamanya, kami ucapkan terimakasih.</p>
        <?php else: ?>
            <p>
                Berdasarkan hasil seleksi berkas dan mempertimbangkan kapasitas kuota bimbingan pada divisi terkait, dengan berat hati kami menginformasikan bahwa permohonan tersebut <b>belum dapat kami penuhi</b> untuk periode yang diajukan.
            </p>
            <p>Demikian informasi ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terimakasih.</p>
        <?php endif; ?>

        <div class="footer-sign-wrap">
            <div class="tembusan-left">
                Tembusan:
                <ol>
                    <li>Manager <?= ($pendaftar->status == 'diterima') ? $nama_divisi : 'Terkait' ?> PT Krakatau IT</li>
                    <li>Ybs</li>
                    <li>Arsip</li>
                </ol>
            </div>

            <div class="ttd-right">
                <b>HUMAN CAPITAL & SM</b><br>
                KRAKATAU TECHNOLOGY,
                <br><br><br><br><br>
                <b>ASEP KUNKUN K.</b><br>
                Manager
            </div>

            <div style="clear: both;"></div>
        </div>
    </div>

    <footer>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr valign="top">
                <td width="35%">
					<b>PT Krakatau Information Technology</b><br>
					Krakatau Steel Building 9<sup>th</sup> Floor,<br>
					Jl. Jend. Gatot Subroto Kav, 54 Jakarta 12950<br><br>
					<span style="color:#0000FF; font-weight:bold;">P</span> +62 21 - 5200732
				</td>
				<td width="35%" style="padding-left: 10px;">
					<b>Cilegon Office</b><br>
					Jl. Raya Anyer Km. 3 Cilegon 42441,<br>
					Banten - Indonesia<br><br>
					<span style="color:#0000FF; font-weight:bold;">P</span> +62 254 - 8317021<br><br>
					<b>www.krakatau-it.co.id</b>
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
