<?php
session_start();
include('koneksi.php'); // Pastikan koneksi ke database sudah ada

// Ambil data dari POST
$schedule_id = $_POST['schedule_id'];
$selected_seats_json = $_POST['seats'];
$total_price = $_POST['total_price'];
$user_id = trim($_POST['user_id']); // Menghapus spasi di sekitar user_id
$payment_method = $_POST['payment_method'];
$nama_bank = isset($_POST['nama_bank']) ? $_POST['nama_bank'] : null;
$no_rek = isset($_POST['no_rek_user']) ? $_POST['no_rek_user'] : null;

// Unggah Bukti Pembayaran hanya jika metode pembayaran bukan Cash
if ($payment_method !== "Cash" && isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == 0) {
    $bukti_pembayaran = file_get_contents($_FILES['bukti_pembayaran']['tmp_name']);
} else {
    $bukti_pembayaran = null; // Tidak ada bukti pembayaran jika Cash
}

// Dekode JSON menjadi array
$selected_seats = json_decode($selected_seats_json, true);
$jumlah_kursi = count($selected_seats);

// Cek apakah jadwal ada di database
$scheduleQuery = "SELECT * FROM tb_schedule WHERE schedule_id = ?";
$stmt = $conn->prepare($scheduleQuery);
$stmt->bind_param("s", $schedule_id);
$stmt->execute();
$scheduleResult = $stmt->get_result();

if ($scheduleResult->num_rows === 0) {
    die("Jadwal tidak ditemukan."); // Error jika jadwal tidak ditemukan
}

// Simpan detail booking ke dalam tabel tb_booking
$bookingQuery = "INSERT INTO tb_booking (user_id, schedule_id, jumlah_kursi) VALUES (?, ?, ?)";
$bookingStmt = $conn->prepare($bookingQuery);
$bookingStmt->bind_param("ssi", $user_id, $schedule_id, $jumlah_kursi);

if (!$bookingStmt->execute()) {
    die("Error executing booking query: " . $bookingStmt->error);
}

// Ambil ID booking yang baru saja dibuat
$booking_id = $bookingStmt->insert_id; // Ambil ID numerik booking

// Simpan transaksi ke dalam tabel tb_transaksi
$transaksi_id = "TR" . str_pad($booking_id, 5, "0", STR_PAD_LEFT); // Contoh format ID transaksi

// Simpan transaksi untuk setiap detail booking
foreach ($selected_seats as $seat_id) {
    // Cek apakah seat_id valid
    $seatCheckQuery = "SELECT * FROM tb_seat WHERE seat_id = ?";
    $seatCheckStmt = $conn->prepare($seatCheckQuery);
    $seatCheckStmt->bind_param("s", $seat_id);
    $seatCheckStmt->execute();
    $seatCheckResult = $seatCheckStmt->get_result();

    if ($seatCheckResult->num_rows === 0) {
        die("Error: seat_id '$seat_id' tidak ditemukan di tb_seat.");
    }

    // Simpan detail booking ke dalam tabel tb_booking_detail
    $bookingDetailQuery = "INSERT INTO tb_booking_detail (booking_id, seat_id, kode_va) VALUES (?, ?, ?)";
    $bookingDetailStmt = $conn->prepare($bookingDetailQuery);
    $kode_va = null; // Atur kode_va sesuai kebutuhan
    $bookingDetailStmt->bind_param("iss", $booking_id, $seat_id, $kode_va);

    if (!$bookingDetailStmt->execute()) {
        die("Error executing booking detail query: " . $bookingDetailStmt->error);
    }

    // Update status kursi menjadi 'Booked'
    $updateSeatQuery = "UPDATE tb_seat SET status = 'Booked' WHERE seat_id = ?";
    $updateSeatStmt = $conn->prepare($updateSeatQuery);
    $updateSeatStmt->bind_param("s", $seat_id);

    if (!$updateSeatStmt->execute()) {
        die("Error updating seat status: " . $updateSeatStmt->error);
    }
}

// Simpan transaksi ke dalam tabel tb_transaksi
$transaksiQuery = "INSERT INTO tb_transaksi (transaksi_id, booking_id, payment_method, nama_bank, no_rek, total_price, bukti_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?)";
$transaksiStmt = $conn->prepare($transaksiQuery);
$transaksiStmt->bind_param("sisssis", $transaksi_id, $booking_id, $payment_method, $nama_bank, $no_rek, $total_price, $bukti_pembayaran);

if (!$transaksiStmt->execute()) {
    die("Error executing transaction query: " . $transaksiStmt->error);
}

echo "<script>
    alert('Pemesanan berhasil!');
    window.location.href = 'home.php'; // Redirect setelah alert
</script>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pemesanan</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ganti dengan file CSS Anda -->
    <script>
        // Tampilkan pesan alert setelah halaman dimuat
        window.onload = function() {
            alert("<?php echo $alertMessage; ?>");
        };
    </script>
</head>

<body>
    <div class="container">
        <h1>Pemesanan Tiket</h1>
        <p>Terima kasih telah melakukan pemesanan!</p>
        <button onclick="window.location.href='home.php';" class="btn btn-dark">Kembali ke Halaman Utama</button> <!-- Tombol Kembali -->
    </div>

    <script>
        // Tampilkan pesan alert setelah halaman dimuat
        window.onload = function() {
            alert("<?php echo $alertMessage; ?>");
        };
    </script>
</body>

</html>