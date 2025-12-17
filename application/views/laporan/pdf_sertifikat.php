<!DOCTYPE html>
<html>
<head>
    <style>
        @page { 
            margin: 0px; 
            size: A4 landscape;
        }
        body { 
            margin: 0px; 
            padding: 0px;
            font-family: 'Helvetica', sans-serif;
            width: 100%;
            height: 100%;
        }
        
        /* Gambar Background dijadikan layer paling bawah */
        .bg-template {
            position: fixed; /* Atau absolute */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* PENTING: Agar ada di belakang teks */
        }

        /* Container Teks */
        .content-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            text-align: center;
            z-index: 1; /* Di atas gambar */
        }

        /* 1. NAMA PESERTA */
        .nama-peserta {
            position: absolute;
            width: 80%;
            left: 10%;
            top: 42%; /* Geser nilai ini untuk naik/turun */
            
            font-weight: 900; 
            font-size: 46pt; 
            color: #855b34; 
            text-transform: uppercase;
            line-height: 1;
        }

        /* 2. PERIODE & KALIMAT */
        .periode-text {
            position: absolute;
            width: 70%;
            left: 15%;
            top: 58%; /* Geser nilai ini untuk naik/turun */
            
            font-size: 14pt;
            color: #000000;
            line-height: 1.5;
        }

        /* 3. TANGGAL SERTIFIKAT */
        .tanggal-sertif {
            position: absolute;
            width: 100%;
            
            /* ATUR NAIK/TURUN DISINI */
            /* Semakin BESAR %, semakin ke ATAS */
            /* Semakin KECIL %, semakin ke BAWAH */
            bottom: 27%;  /* Sebelumnya 22%, saya naikkan jadi 25% */
            
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            text-align: center; 
        }
        
        /* ATUR KIRI/KANAN DISINI */
        .geser-kanan {
            display: inline-block;
            
            /* Semakin BESAR px, semakin ke KANAN */
            /* Semakin KECIL px, semakin ke KIRI */
            margin-left: -10px; /* Sebelumnya 250px, saya kurangi biar geser kiri */
        }
    </style>
</head>
<body>

    <?php if(!empty($background_base64)): ?>
        <img src="<?= $background_base64 ?>" class="bg-template">
    <?php else: ?>
        <div style="position:absolute; top:10px; left:10px; color:red;">
            Background Image Not Found / Error Loading Base64
        </div>
    <?php endif; ?>

    <div class="content-layer">
        
        <div class="nama-peserta">
            <?= $nama ?>
        </div>

        <div class="periode-text">
            <?= $periode_text ?>
        </div>

        <div class="tanggal-sertif">
            <span class="geser-kanan">
                <?= $tanggal_sertifikat ?>
            </span>
        </div>

    </div>

</body>
</html>
