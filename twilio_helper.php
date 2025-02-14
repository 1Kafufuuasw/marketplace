<?php
require_once 'vendor/autoload.php';
$sid           = 'ACebb8bcdb2d3cf044c689900971aa7f98';
$auth_token    = '75855d7d2069f6619fe98535a713971a';
$twilio_number = '+14155238886'; // tanpa "whatsapp:" di sini

// Inisialisasi Twilio Client
$client = new \Twilio\Rest\Client($sid, $auth_token);

// Format nomor tujuan
$destinationNumber = "whatsapp:" . $no_hp;
$messageBody = "Halo $buyer_name, pesanan Anda sebanyak $jumlah x " . $tanaman['nama'] . " berhasil diproses.";

try {
    $message = $client->messages->create(
        $destinationNumber,
        [
            'from' => "whatsapp:$twilio_number", // menghasilkan "whatsapp:+14155238886"
            'body' => $messageBody
        ]
    );
    error_log("Twilio Message SID: " . $message->sid);
} catch (Exception $e) {
    error_log("Twilio Error: " . $e->getMessage());
    $errors[] = "Terjadi kesalahan saat mengirim notifikasi WhatsApp: " . $e->getMessage();
}
?>