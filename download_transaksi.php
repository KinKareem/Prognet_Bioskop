<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit;
}

// Ambil ID transaksi dari parameter URL
$transaksi_id = $_GET['transaksi_id'];

// Query untuk mengambil detail kursi dan nama pemesan berdasarkan ID transaksi
$query = "
    SELECT 
        m.judul AS film_judul, 
        s.tanggal, 
        s.waktu_mulai, 
        sd.nomor_kursi,
        u.nama AS user_nama
    FROM 
        tb_transaksi t 
    JOIN 
        tb_booking b ON t.booking_id = b.booking_id 
    JOIN 
        tb_schedule s ON b.schedule_id = s.schedule_id 
    JOIN 
        tb_movie m ON s.movie_id = m.movie_id 
    JOIN 
        tb_booking_detail bd ON b.booking_id = bd.booking_id 
    JOIN 
        tb_seat sd ON bd.seat_id = sd.seat_id 
    JOIN 
        tb_user u ON b.user_id = u.user_id
    WHERE 
        t.transaksi_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $transaksi_id); // Mengikat parameter
$stmt->execute();
$result = $stmt->get_result();

// Jika tidak ada data untuk ID transaksi tersebut
if ($result->num_rows == 0) {
    echo "Tidak ada data tiket untuk ID transaksi ini.";
    exit;
}

// Menggunakan Dompdf untuk membuat PDF
require 'vendor/autoload.php'; // Pastikan path ini sesuai dengan lokasi vendor

use Dompdf\Dompdf;

// Buat instance Dompdf
$dompdf = new Dompdf();

// Konten HTML untuk PDF
$html = '
<style>
    .ticket-card {
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
    }
    .seat-number {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }
    h2 {
        text-align: center;
    }
    .header {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>
<h2>Detail Tiket</h2>';

// Ambil nama pemesan dari hasil query
$row = $result->fetch_assoc();
$user_name = htmlspecialchars($row['user_nama']);
$html .= '<div class="header">Nama Pemesan: ' . $user_name . '</div>'; // Menampilkan nama pemesan

// Menambahkan detail tiket
do {
    $html .= '<div class="ticket-card">';
    $html .= '<p class="seat-number">' . htmlspecialchars($row['nomor_kursi']) . '</p>'; // Nomor kursi di atas kanan
    $html .= '<p><strong>Judul Film:</strong> ' . htmlspecialchars($row['film_judul']) . '</p>';
    $html .= '<p><strong>Tanggal:</strong> ' . htmlspecialchars($row['tanggal']) . '</p>';
    $html .= '<p><strong>Waktu:</strong> ' . htmlspecialchars($row['waktu_mulai']) . '</p>';
    $html .= '</div>';
} while ($row = $result->fetch_assoc());

// Load konten HTML ke Dompdf
$dompdf->loadHtml($html);

// (Optional) Set ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF dengan ID transaksi sebagai nama file
$dompdf->stream('tiket_film_' . $transaksi_id . '.pdf', array('Attachment' => true)); // 'Attachment' => true untuk download
exit;
?>