<?php
// Set password rahsia kau kat sini
$password_rahsia = "kipassejuk123"; 
$mesej = "";

if(isset($_POST['submit'])){
    if($_POST['password'] != $password_rahsia) {
        $mesej = "<p style='color:red;'>Password salah bro! Cuba lagi.</p>";
    } else {
        $target_dir = "galeri-job/";
        
        // 1. Ambil kata kunci SEO dari form
        $keyword_raw = $_POST['seoKeyword'];
        $keyword_clean = strtolower(trim($keyword_raw));
        $keyword_clean = preg_replace('/[^a-z0-9-]+/', '-', $keyword_clean);
        $keyword_clean = preg_replace('/-+/', '-', $keyword_clean);
        
        if(empty($keyword_clean)) {
            $keyword_clean = "mat-kipas-sejuk";
        }

        // Ambil tarikh hari ini untuk bezakan batch upload (cth: 20260708)
        $tarikh = date('Ymd');

        // 2. Kira berapa banyak gambar yang di-upload sekaligus
        $total_files = count($_FILES['gambarJob']['name']);
        $berjaya_count = 0;
        $gagal_count = 0;

        // Loop untuk proses setiap gambar satu-demi-satu
        for($i = 0; $i < $total_files; $i++) {
            $file_tmp = $_FILES["gambarJob"]["tmp_name"][$i];
            
            // Skip kalau fail kosong
            if(empty($file_tmp)) continue;

            // Generate nombor urutan (001, 002, 003...)
            $running_number = sprintf("%03d", $i + 1);

            // Cantumkan nama fail baru (Keyword + Tarikh + Running Number + .jpg)
            $nama_fail_baru = $keyword_clean . "-" . $tarikh . "-" . $running_number . ".jpg";
            $target_file = $target_dir . $nama_fail_baru;

            // Proses Auto-Resize & Compress
            $check = getimagesize($file_tmp);
            
            if($check !== false) {
                $width_asal = $check[0];
                $height_asal = $check[1];
                
                $max_width = 1024; // Maksimum lebar gambar
                
                if ($width_asal > $max_width) {
                    $ratio = $max_width / $width_asal;
                    $new_width = $max_width;
                    $new_height = $height_asal * $ratio;
                } else {
                    $new_width = $width_asal;
                    $new_height = $height_asal;
                }

                $image_data = file_get_contents($file_tmp);
                $source_image = imagecreatefromstring($image_data);
                
                if ($source_image !== false) {
                    $destination_image = imagecreatetruecolor($new_width, $new_height);
                    imagecopyresampled($destination_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width_asal, $height_asal);
                    
                    // Simpan ke folder
                    if(imagejpeg($destination_image, $target_file, 80)) {
                        $berjaya_count++;
                    } else {
                        $gagal_count++;
                    }
                    
                    imagedestroy($source_image);
                    imagedestroy($destination_image);
                } else {
                    $gagal_count++;
                }
            } else {
                $gagal_count++;
            }
        }

        // Tulis mesej status keseluruhan upload
        $mesej = "<p style='color:green; font-weight:bold;'>Selesai Proses! $berjaya_count gambar berjaya disave & di-resize.</p>";
        if($gagal_count > 0) {
            $mesej .= "<p style='color:red;'>Ada $gagal_count gambar bermasalah/gagal.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Multi-Upload - Mat Kipas Sejuk</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 500px; margin: 0 auto; background: #eaeaea; }
        .upload-box { border: 1px solid #ccc; padding: 20px; border-radius: 10px; background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input[type="file"], input[type="password"], input[type="text"] { display: block; margin-bottom: 15px; width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        button { background: #28a745; color: white; padding: 12px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; }
        button:hover { background: #218838; }
        .hint { font-size: 12px; color: #666; margin-top: -10px; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>

    <div class="upload-box">
        <h2>Upload Gambar (Lazy Mode 🚀)</h2>
        <?php echo $mesej; ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <label><strong>Pilih Gambar (Boleh pilih banyak sekaligus):</strong></label>
            <input type="file" name="gambarJob[]" id="gambarJob" accept="image/png, image/jpeg, image/jpg" multiple required>
            
            <label><strong>Nama Urusan / Lokasi (Satu keyword untuk semua):</strong></label>
            <input type="text" name="seoKeyword" placeholder="Cth: sewa air cooler kulai" required>
            <span class="hint">Sistem akan auto-rename jadi: <code>sewa-air-cooler-kulai-<?php echo date('Ymd'); ?>-001.jpg</code> dan seterusnya.</span>
            
            <label><strong>Password Admin:</strong></label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            
            <button type="submit" name="submit">Upload Semua Sekaligus</button>
        </form>
    </div>

</body>
</html>
