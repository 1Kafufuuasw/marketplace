<?php
// dashboard_admin.php
require 'config.php';
if(!isset($_SESSION['admin_id'])){
    header("Location: login_admin.php");
    exit;
}

// Hapus toko jika ada parameter delete_toko
if(isset($_GET['delete_toko'])){
    $delete_id = intval($_GET['delete_toko']);
    $stmt = $pdo->prepare("DELETE FROM toko WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: dashboard_admin.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM toko");
$tokoList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Dashboard Admin</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Toko</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($tokoList as $toko): ?>
          <tr>
            <td><?= $toko['id'] ?></td>
            <td><?= htmlspecialchars($toko['nama_toko']) ?></td>
            <td>
              <a href="dashboard_admin.php?delete_toko=<?= $toko['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus toko ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
