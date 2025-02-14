<?php
// dashboard_toko.php
require 'config.php';
if(!isset($_SESSION['toko_id'])){
    header("Location: login_toko.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Toko - Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama_toko']) ?></h2>
    <div class="mt-4">
      <a href="jual_tanaman.php" class="btn btn-success">Jual Tanaman</a>
      <a href="penjualan.php" class="btn btn-info">Penjualan</a>
    </div>
  </div>
</body>
</html>
