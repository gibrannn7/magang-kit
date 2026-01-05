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
        
        .bg-template {
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; 
        }

        .content-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            text-align: center;
            z-index: 1;
        }

        .nama-peserta {
            position: absolute;
            width: 80%;
            left: 10%;
            top: 42%; 
            
            font-weight: 900; 
            font-size: 46pt; 
            color: #855b34; 
            text-transform: uppercase;
            line-height: 1;
        }

        .periode-text {
            position: absolute;
            width: 70%;
            left: 15%;
            top: 58%; 
            
            font-size: 14pt;
            color: #000000;
            line-height: 1.5;
        }

        .tanggal-sertif {
            position: absolute;
            width: 100%;
            bottom: 27%;  
            
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            text-align: center; 
        }
        
        .geser-kanan {
            display: inline-block;
            margin-left: -10px;
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
