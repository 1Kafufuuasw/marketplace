<?php
// register.php
require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($nama_toko) || empty($password) || empty($confirm_password)){
        $errors[] = "Semua field harus diisi.";
    } elseif($password !== $confirm_password){
        $errors[] = "Password dan konfirmasi tidak cocok.";
    } else {
        // Cek apakah nama_toko sudah ada
        $stmt = $pdo->prepare("SELECT * FROM toko WHERE nama_toko = ?");
        $stmt->execute([$nama_toko]);
        if($stmt->rowCount() > 0){
            $errors[] = "Nama toko sudah terdaftar.";
        } else {
            // Insert toko baru
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO toko (nama_toko, password) VALUES (?, ?)");
            if($stmt->execute([$nama_toko, $hashedPassword])){
                header("Location: login_toko.php");
                exit;
            } else {
                $errors[] = "Gagal mendaftarkan toko.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register - Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Register Toko</h2>
    <?php
    if(!empty($errors)){
        echo '<div class="alert alert-danger">';
        foreach($errors as $error){
            echo '<p>'.htmlspecialchars($error).'</p>';
        }
        echo '</div>';
    }
    ?>
    <form method="POST">
      <div class="form-group">
        <label>Nama Toko</label>
        <input type="text" name="nama_toko" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Konfirmasi Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>
  </div>
</body>
</html>
