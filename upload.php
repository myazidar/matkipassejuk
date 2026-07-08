<?php
// Set password rahsia kau kat sini
$password_rahsia = "kipassejuk123"; 

$mesej = "";

if(isset($_POST['submit'])){
    // Semak password dulu
    if($_POST['password'] != $password_rahsia) {
        $mesej = "<p style='color:red;'>Password salah bro! Cuba lagi.</p>";
    } else {
        $target_dir = "galeri-job/";
        
        // 1. Ambil keyword SEO dari form
        $keyword_raw = $_POST['seoKeyword'];
        
        // 2. Bersihkan keyword (tukar huruf kecil, ganti space dengan dash, buang simbol pelik)
        $keyword_clean = strtolower(trim($keyword_raw));
        $keyword_clean = preg_replace('/[^a-z0-9-]+/', '-', $keyword_clean);
        $keyword_clean = preg_replace('/-+/', '-', $keyword_clean); // buang dash double
        
        // Kalau tak letak keyword, kita set default "mat-kipas-sejuk"
        if(empty($keyword_clean)) {
            $keyword_clean = "mat-kipas-sejuk";
        }

        // 3. Ambil file extension (contoh: .jpg, .png)
        $nama_fail_asal = basename($_FILES["gambarJob"]["name"]);
        $imageFileType = strtolower(pathinfo($nama_fail_asal, PATHINFO_EXTENSION));
        
        // 4. Cantumkan jadi nama fail baru (Keyword SEO + Timestamp + Extension)
        $nama_fail_baru = $keyword_clean . "-" . time() . "." . $imageFileType;
        $target_file = $target_dir . $nama_fail_baru;

        // Pastikan fail tu betul-betul gambar
        $check = getimagesize($_FILES["gambarJob"]["tmp_name"]);
        if($check !== false) {
            // Proses upload ke folder galeri-job/
            if (move_uploaded_file($_FILES["gambarJob"]["tmp_name"], $target_file)) {
                $mesej = "<p style='color:green;'>Terbaik! Gambar berjaya di-upload dengan nama:<br><strong>" . $nama_fail_baru . "</strong></p>";
            } else {
                $mesej = "<p style='color:red;'>Alamak, ada error masa nak save gambar kat server.</p>";
            }
        } else {
            $mesej = "<p style='color:red;'>Gagal. Pastikan fail yang di-upload tu adalah gambar.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Upload - Mat Kipas Sejuk</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 500px; margin: 0 auto; }
        .upload-box { border: 1px solid #ccc; padding: 20px; border-radius: 10px; background: #f9f9f9; }
        input[type="file"], input[type="password"], input[type="text"] { display: block; margin-bottom: 15px; width: 100%; padding: 10px; box-sizing: border-box; }
        button { background: #007BFF; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background: #0056b3; }
        .hint { font-size: 12px; color: #666; margin-top: -10px; margin-bottom: 15px; display: block; }
    </style>
</head>
<body>

    <div class="upload-box">
        <h2>Upload Gambar Job 📸</h2>
        <?php echo $mesej; ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <label><strong>Pilih Gambar:</strong></label>
            <input type="file" name="gambarJob" id="gambarJob" accept="image/png, image/jpeg, image/jpg" required>
            
            <label><strong>Nama Job / Lokasi (Untuk SEO):</strong></label>
            <input type="text" name="seoKeyword" placeholder="Cth: Sewa Air Cooler Majlis Kahwin JB" required>
            <span class="hint">Nama ini akan dijadikan nama fail gambar untuk tarik trafik Google.</span>
            
            <label><strong>Password Admin:</strong></label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            
            <button type="submit" name="submit">Upload Sekarang</button>
        </form>
    </div>

</body>
</html>
