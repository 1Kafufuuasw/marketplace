<?php
// checkout.php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$tanaman_id = intval($_GET['id']);

// Ambil data tanaman
$stmt = $pdo->prepare("SELECT * FROM tanaman WHERE id = ?");
$stmt->execute([$tanaman_id]);
$tanaman = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tanaman) {
    echo "Tanaman tidak ditemukan.";
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_name = trim($_POST['buyer_name']);
    $alamat     = trim($_POST['alamat']);
    $no_hp      = trim($_POST['no_hp']);
    $jumlah     = intval($_POST['jumlah']);

    if (empty($buyer_name) || empty($alamat) || empty($no_hp) || $jumlah <= 0) {
        $errors[] = "Semua field harus diisi dengan benar.";
    } elseif (($tanaman['stok_terjual'] + $jumlah) > $tanaman['stok_total']) {
        $errors[] = "Stok tidak mencukupi.";
    } else {
        // Insert ke tabel penjualan
        $stmt = $pdo->prepare("INSERT INTO penjualan (tanaman_id, buyer_name, alamat, no_hp, jumlah) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$tanaman_id, $buyer_name, $alamat, $no_hp, $jumlah])) {
            // Update stok_terjual
            $stmtUpdate = $pdo->prepare("UPDATE tanaman SET stok_terjual = stok_terjual + ? WHERE id = ?");
            $stmtUpdate->execute([$jumlah, $tanaman_id]);

            // Konversi nomor HP ke format E.164 jika belum diawali '+'
            if (substr($no_hp, 0, 1) !== '+') {
                if (substr($no_hp, 0, 1) === '0') {
                    $no_hp = '+62' . substr($no_hp, 1);
                } else {
                    $no_hp = '+' . $no_hp;
                }
            }

            // Hitung total harga dan format harga satuan serta total harga
            $totalHarga  = $jumlah * $tanaman['harga'];
            $hargaSatuan = number_format($tanaman['harga'], 2, ',', '.');
            $totalHargaF = number_format($totalHarga, 2, ',', '.');

            // Kirim notifikasi via Twilio
            // Pastikan Twilio SDK sudah terinstall melalui Composer: composer require twilio/sdk
            require_once 'vendor/autoload.php';
            $sid           = 'ACebb8bcdb2d3cf044c689900971aa7f98';
            $auth_token    = '75855d7d2069f6619fe98535a713971a';
            $twilio_number = '+14155238886'; // Nomor WhatsApp Twilio (tanpa prefix "whatsapp:")

            // Inisialisasi Twilio Client
            $client = new \Twilio\Rest\Client($sid, $auth_token);

            // Format nomor tujuan (harus diawali dengan "whatsapp:" dan format E.164)
            $destinationNumber = "whatsapp:" . $no_hp;
            $messageBody = "Hallo $buyer_name, terimakasih telah memesan " 
                . $tanaman['nama'] 
                . ". Jumlah: $jumlah, harga satuan: Rp $hargaSatuan, total harga: Rp $totalHargaF. Terimakasih telah membeli produk di tempat ini.";

            try {
                $message = $client->messages->create(
                    $destinationNumber,
                    [
                        'from' => "whatsapp:" . $twilio_number,
                        'body' => $messageBody
                    ]
                );
                // Debug: catat SID pesan ke log
                error_log("Twilio Message SID: " . $message->sid);
            } catch (Exception $e) {
                // Jika terjadi error pada pengiriman pesan, log error tersebut dan tampilkan error ke pengguna
                error_log("Twilio Error: " . $e->getMessage());
                $errors[] = "Terjadi kesalahan saat mengirim notifikasi WhatsApp: " . $e->getMessage();
            }

            // Jika tidak ada error, redirect ke halaman konfirmasi
            if (empty($errors)) {
                header("Location: selamat.php");
                exit;
            }
        } else {
            $errors[] = "Gagal memproses pesanan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Checkout - Marketplace Tanaman</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-5">
    <h2>Checkout - <?= htmlspecialchars($tanaman['nama']) ?></h2>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
          <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Nama Pemesan</label>
        <input type="text" name="buyer_name" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required></textarea>
      </div>
      <div class="form-group">
        <label>No HP (Whatsapp)</label>
        <input type="text" name="no_hp" class="form-control" required>
        <small class="form-text text-muted">Masukkan nomor, misalnya: 08123456789</small>
      </div>
      <div class="form-group">
        <label>Jumlah Tanaman</label>
        <input type="number" name="jumlah" class="form-control" min="1" required>
      </div>
      <button type="submit" class="btn btn-primary">Konfirmasi</button>
    </form>
  </div>
</body>
</html>
