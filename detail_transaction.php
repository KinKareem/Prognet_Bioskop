<?php
include 'koneksi.php'; // Koneksi ke database

if (isset($_GET['id'])) {
    $transaksi_id = $_GET['id'];

    // Query untuk mendapatkan detail transaksi
    $sql = "SELECT t.*, u.nama AS user_nama, u.email, 
                   m.judul AS film_judul, 
                   s.tanggal, s.waktu_mulai, 
                   GROUP_CONCAT(se.nomor_kursi SEPARATOR ', ') AS kursi
            FROM tb_transaksi t 
            JOIN tb_booking b ON t.booking_id = b.booking_id 
            JOIN tb_user u ON b.user_id = u.user_id 
            JOIN tb_schedule s ON b.schedule_id = s.schedule_id 
            JOIN tb_movie m ON s.movie_id = m.movie_id 
            JOIN tb_booking_detail bd ON b.booking_id = bd.booking_id 
            JOIN tb_seat se ON bd.seat_id = se.seat_id 
            WHERE t.transaksi_id = ?
            GROUP BY t.transaksi_id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $transaksi_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transaksi = $result->fetch_assoc();
        // Tampilkan detail transaksi
        echo "<h2>Detail Transaksi</h2>";
        echo "Nama: " . htmlspecialchars($transaksi['user_nama']) . "<br>";
        echo "Email: " . htmlspecialchars($transaksi['email']) . "<br>";
        echo "Nama Film: " . htmlspecialchars($transaksi['film_judul']) . "<br>";
        echo "Tanggal: " . htmlspecialchars($transaksi['tanggal']) . "<br>";
        echo "Waktu Mulai: " . htmlspecialchars($transaksi['waktu_mulai']) . "<br>";
        echo "Kursi yang Dipesan: " . htmlspecialchars($transaksi['kursi']) . "<br>";
        echo "Total Harga: Rp " . number_format($transaksi['total_price'], 2, ',', '.') . "<br>";
        echo "Status: " . htmlspecialchars($transaksi['status']) . "<br>";
        echo "Tanggal Transaksi: " . htmlspecialchars($transaksi['tanggal_transaksi']) . "<br>";

        // Menampilkan bukti pembayaran
        if (!empty($transaksi['bukti_pembayaran'])) {
            echo "Bukti Pembayaran: <br>";
            echo "<img src='data:image/jpeg;base64," . base64_encode($transaksi['bukti_pembayaran']) . "' alt='Bukti Pembayaran' style='max-width: 300px;'><br>";
        } else {
            echo "Bukti Pembayaran: Tidak ada bukti pembayaran yang diunggah.<br>";
        }
    } else {
        echo "Transaksi tidak ditemukan.";
    }
}

// Menutup koneksi
$conn->close();
?>