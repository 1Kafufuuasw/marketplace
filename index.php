<?php
// index.php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* Header Style yang Lebih Soft dan Modern */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background-color: #ffffff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .header a.brand {
      text-decoration: none;
      color: #007bff;
      font-size: 28px;
      font-weight: 600;
      transition: color 0.3s;
    }
    .header a.brand:hover {
      color: #0056b3;
    }
    .header a.btn {
      font-size: 16px;
      padding: 8px 16px;
      border-radius: 20px;
    }

    /* Banner Carousel */
    .banner {
      margin: 20px 0;
      overflow: hidden;
    }
    .banner img {
      width: 100%;
      height: auto;
      max-height: 100vh; /* Agar tidak melebihi tinggi layar */
      object-fit: cover;
    }
    
    /* Tampilan produk tiap toko */
    .product-row {
      display: flex;
      overflow-x: auto;
      padding: 10px 0;
    }
    .product-card {
      flex: 0 0 auto;
      margin-right: 15px;
      border: 1px solid #ddd;
      padding: 10px;
      border-radius: 5px;
      background: #fff;
    }
    .product-card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    /* Responsive: 2 kolom untuk layar kecil, 5 kolom untuk layar besar */
    @media (min-width: 576px) {
      .product-card {
        width: calc((100% - 30px) / 2);
      }
    }
    @media (min-width: 992px) {
      .product-card {
        width: calc((100% - 60px) / 5);
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header">
    <a href="index.php" class="brand">Marketplace bayyu awselebew</a>
    <a href="login.php" class="btn btn-primary">Login</a>
  </div>

  <!-- Banner Carousel -->
  <div id="bannerCarousel" class="carousel slide banner" data-ride="carousel" data-interval="6000">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="./images/banner_tanaman2.jpg" class="d-block w-100" alt="Banner 1">
      </div>
      <div class="carousel-item">
        <img src="./images/banner_tanaman3.jpg" class="d-block w-100" alt="Banner 2">
      </div>
    </div>
    <a class="carousel-control-prev" href="#bannerCarousel" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </a>
    <a class="carousel-control-next" href="#bannerCarousel" role="button" data-slide="next">
      <span class="carousel-control-next-icon"></span>
    </a>
  </div>
  <br><br><h2>dua nol dibelakang koma anggap aja ga ada </h2><br><br>

  <!-- Produk per Toko -->
  <div class="container">
    <?php
      // Ambil semua toko
      $stmt = $pdo->query("SELECT * FROM toko");
      $tokoList = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($tokoList as $toko) {
        echo '<section class="mb-4">';
        echo '<h3>' . htmlspecialchars($toko['nama_toko']) . '</h3>';
        
        // Ambil produk tanaman dari toko ini
        $stmt2 = $pdo->prepare("SELECT * FROM tanaman WHERE toko_id = ?");
        $stmt2->execute([$toko['id']]);
        $tanamanList = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        if ($tanamanList) {
          echo '<div class="product-row">';
          foreach ($tanamanList as $tanaman) {
            echo '<div class="product-card">';
            echo '<img src="./uploads/' . htmlspecialchars($tanaman['image']) . '" alt="' . htmlspecialchars($tanaman['nama']) . '">';
            echo '<h5>' . htmlspecialchars($tanaman['nama']) . '</h5>';
            echo '<p>Rp ' . number_format($tanaman['harga'], 2, ',', '.') . '</p>';
            echo '<p>Stok: (' . $tanaman['stok_terjual'] . '/' . $tanaman['stok_total'] . ')</p>';
            echo '<a href="detail_tanaman.php?id=' . $tanaman['id'] . '" class="btn btn-sm btn-info">Detail</a>';
            echo '</div>';
          }
          echo '</div>';
        } else {
          echo '<p>Toko ini belum memiliki produk.</p>';
        }
        echo '</section>';
      }
    ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
