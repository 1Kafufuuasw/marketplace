<?php
// jual_tanaman.php
require 'config.php';
if(!isset($_SESSION['toko_id'])){
    header("Location: login_toko.php");
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = floatval($_POST['harga']);
    $stok_total = intval($_POST['stok_total']);
    
    // Validasi input teks
    if(empty($nama) || empty($harga) || empty($stok_total)){
        $errors[] = "Nama, harga, dan stok harus diisi.";
    }
    
    // Validasi file upload gambar
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        
        if(in_array($fileExtension, $allowedfileExtensions)) {
            // Generate nama file baru secara unik
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            // Buat folder uploads jika belum ada
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;
            if(!move_uploaded_file($fileTmpPath, $dest_path)) {
                $errors[] = "Terjadi error saat mengupload file. Periksa permission folder.";
            }
        } else {
            $errors[] = "Tipe file tidak diperbolehkan. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }
    } else {
        $errors[] = "Gambar harus diupload.";
    }
    
    // Jika tidak ada error, masukkan data ke database
    if(empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO tanaman (toko_id, nama, deskripsi, harga, stok_total, image) VALUES (?, ?, ?, ?, ?, ?)");
        if($stmt->execute([$_SESSION['toko_id'], $nama, $deskripsi, $harga, $stok_total, $newFileName])){
            header("Location: dashboard_toko.php");
            exit;
        } else {
            $errors[] = "Gagal menambahkan tanaman.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Jual Tanaman - Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Jual Tanaman</h2>
    <?php
    if(!empty($errors)){
        echo '<div class="alert alert-danger">';
        foreach($errors as $error){
            echo '<p>'.htmlspecialchars($error).'</p>';
        }
        echo '</div>';
    }
    ?>
    <!-- Pastikan form menggunakan enctype multipart/form-data -->
    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Nama Tanaman</label>
        <input type="text" name="nama" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control"></textarea>
      </div>
      <div class="form-group">
        <label>Harga</label>
        <input type="number" step="0.01" name="harga" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Stok Total</label>
        <input type="number" name="stok_total" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Gambar Tanaman</label>
        <input type="file" name="image" class="form-control-file" required>
      </div>
      <button type="submit" class="btn btn-primary">Tambah Tanaman</button>
    </form>
  </div>
</body>
</html>
