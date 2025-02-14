<?php
// login_toko.php
require 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_toko = trim($_POST['nama_toko']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM toko WHERE nama_toko = ?");
    $stmt->execute([$nama_toko]);
    $toko = $stmt->fetch(PDO::FETCH_ASSOC);

    if($toko && password_verify($password, $toko['password'])){
        $_SESSION['toko_id'] = $toko['id'];
        $_SESSION['nama_toko'] = $toko['nama_toko'];
        header("Location: dashboard_toko.php");
        exit;
    } else {
        $error = "Nama toko atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Toko - Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Login Toko</h2>
    <?php if($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Nama Toko</label>
        <input type="text" name="nama_toko" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Login</button>
    </form>
  </div>
</body>
</html>
