<?php
// penjualan.php
require 'config.php';
if(!isset($_SESSION['toko_id'])){
    header("Location: login_toko.php");
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, t.nama AS nama_tanaman 
                       FROM penjualan p 
                       JOIN tanaman t ON p.tanaman_id = t.id 
                       WHERE t.toko_id = ?");
$stmt->execute([$_SESSION['toko_id']]);
$penjualanList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Penjualan - Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Data Penjualan</h2>
    <?php if($penjualanList): ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Tanaman</th>
            <th>Nama Pembeli</th>
            <th>Alamat</th>
            <th>No HP</th>
            <th>Jumlah</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($penjualanList as $sale): ?>
            <tr>
              <td><?= $sale['id'] ?></td>
              <td><?= htmlspecialchars($sale['nama_tanaman']) ?></td>
              <td><?= htmlspecialchars($sale['buyer_name']) ?></td>
              <td><?= htmlspecialchars($sale['alamat']) ?></td>
              <td><?= htmlspecialchars($sale['no_hp']) ?></td>
              <td><?= $sale['jumlah'] ?></td>
              <td><?= $sale['tanggal'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Tidak ada data penjualan.</p>
    <?php endif; ?>
  </div>
</body>
</html>
