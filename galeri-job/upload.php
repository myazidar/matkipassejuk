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
        $nama_fail = basename($_FILES["gambarJob"]["name"]);
        
        // Buang space pada nama fail supaya tak error kat server
        $nama_fail_bersih = preg_replace('/\s+/', '_', $nama_fail); 
        $target_file = $target_dir . time() . "_" . $nama_fail_bersih; // Tambah time() supaya nama tak bertindih
        
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Pastikan fail tu betul-betul gambar
        $check = getimagesize($_FILES["gambarJob"]["tmp_name"]);
        if($check !== false) {
            // Proses upload ke folder galeri-job/
            if (move_uploaded_file($_FILES["gambarJob"]["tmp_name"], $target_file)) {
                $mesej = "<p style='color:green;'>Terbaik! Gambar berjaya di-upload.</p>";
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
        input[type="file"], input[type="password"] { display: block; margin-bottom: 15px; width: 100%; padding: 10px; box-sizing: border-box; }
        button { background: #007BFF; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

    <div class="upload-box">
        <h2>Upload Gambar Job 📸</h2>
        <?php echo $mesej; ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <label><strong>Pilih Gambar:</strong></label>
            <input type="file" name="gambarJob" id="gambarJob" accept="image/png, image/jpeg, image/jpg" required>
            
            <label><strong>Password Admin:</strong></label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            
            <button type="submit" name="submit">Upload Sekarang</button>
        </form>
    </div>

</body>
</html>
