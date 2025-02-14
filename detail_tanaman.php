<?php
// detail_tanaman.php
require 'config.php';

if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

$tanaman_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT t.*, tk.nama_toko FROM tanaman t JOIN toko tk ON t.toko_id = tk.id WHERE t.id = ?");
$stmt->execute([$tanaman_id]);
$tanaman = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$tanaman){
    echo "Tanaman tidak ditemukan.";
    exit;
}

// Ambil review jika ada
$stmtReview = $pdo->prepare("SELECT * FROM review WHERE tanaman_id = ?");
$stmtReview->execute([$tanaman_id]);
$reviews = $stmtReview->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Tanaman - <?= htmlspecialchars($tanaman['nama']) ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .detail-container {
      display: flex;
      flex-wrap: wrap;
    }
    .detail-info {
      flex: 1 1 50%;
      padding: 20px;
    }
    .detail-image {
      flex: 1 1 50%;
      padding: 20px;
    }
    .detail-image img {
      width: 100%;
      height: auto;
      object-fit: cover;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="detail-container">
      <div class="detail-info">
        <h2><?= htmlspecialchars($tanaman['nama']) ?></h2>
        <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($tanaman['deskripsi'])) ?></p>
        <p><strong>Harga:</strong> Rp <?= number_format($tanaman['harga'],2,',','.') ?></p>
        <p><strong>Stok:</strong> (<?= $tanaman['stok_terjual'] ?>/<?= $tanaman['stok_total'] ?>)</p>
        <p><strong>Toko:</strong> <?= htmlspecialchars($tanaman['nama_toko']) ?></p>
        <a href="checkout.php?id=<?= $tanaman['id'] ?>" class="btn btn-primary">Checkout</a>
      </div>
      <div class="detail-image">
        <img src="./uploads/<?= htmlspecialchars($tanaman['image']) ?>" alt="<?= htmlspecialchars($tanaman['nama']) ?>">
      </div>
    </div>

    <!-- Tampilan review (opsional) -->
    <?php if($reviews): ?>
      <div class="mt-4">
        <h4>Review</h4>
        <?php foreach($reviews as $review): ?>
          <div class="border p-2 mb-2">
            <p><strong><?= htmlspecialchars($review['nama_reviewer']) ?></strong> (Rating: <?= $review['rating'] ?>/5)</p>
            <p><?= nl2br(htmlspecialchars($review['komentar'])) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
