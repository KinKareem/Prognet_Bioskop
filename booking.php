<?php
session_start();
if (isset($_SESSION['selected_seats'])) {
    unset($_SESSION['selected_seats']); // Hapus kursi yang dipilih sebelumnya
}

include('koneksi.php'); // Pastikan koneksi ke database sudah ada

// Cek apakah data yang diperlukan ada
if (!isset($_POST['schedule_id']) || !isset($_POST['seats'])) {
    die("Data tidak lengkap. Silakan kembali dan coba lagi.");
}

// Ambil data dari POST
$schedule_id = $_POST['schedule_id'];
$selected_seats_json = $_POST['seats']; // Ini adalah kursi yang dipilih

// Dekode JSON menjadi array
$selected_seats = json_decode($selected_seats_json, true);

// Cek apakah jadwal ada di database
$scheduleQuery = "SELECT * FROM tb_schedule WHERE schedule_id = ?";
$stmt = $conn->prepare($scheduleQuery);
$stmt->bind_param("s", $schedule_id);
$stmt->execute();
$scheduleResult = $stmt->get_result();

if ($scheduleResult->num_rows === 0) {
    die("Jadwal tidak ditemukan."); // Error jika jadwal tidak ditemukan
}

// Jika jadwal ditemukan, lanjutkan dengan pemrosesan pemesanan
$schedule = $scheduleResult->fetch_assoc();

// Ambil harga tiket dari jadwal
$total_price = count($selected_seats) * $schedule['harga_tiket']; // Hitung total harga

// Ambil user_id dari session
$user_id = $_SESSION['user_id']; // Pastikan user_id sudah disimpan di session

// Query untuk mendapatkan nomor_kursi berdasarkan id_kursi
$seatQuery = "SELECT seat_id, nomor_kursi FROM tb_seat WHERE seat_id IN (" . implode(',', array_fill(0, count($selected_seats), '?')) . ")";
$stmt = $conn->prepare($seatQuery);
$stmt->bind_param(str_repeat('s', count($selected_seats)), ...$selected_seats);
$stmt->execute();
$seatResult = $stmt->get_result();

// Simpan nomor_kursi dalam array
$seatNumbers = [];
while ($row = $seatResult->fetch_assoc()) {
    $seatNumbers[$row['seat_id']] = $row['nomor_kursi'];
}

// Tampilkan konfirmasi pemesanan
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Tiket</title>
    <link rel="stylesheet" href="booking.css">
    <script>
        function togglePaymentDetails() {
            var paymentMethod = document.getElementById("payment_method").value;
            var bankDetails = document.getElementById("bank-details");

            if (paymentMethod === "Cash") {
                bankDetails.style.display = "none"; // Sembunyikan detail bank dan file upload
            } else {
                bankDetails.style.display = "block"; // Tampilkan detail bank dan file upload
            }
        }
    </script>
</head>

<body>
    <!-------------- header home ---------------->
    <header class="header_home">
        <img src="logo w.png" class="logo_home" alt="">
    </header>

    <!---------- Form Konfirmasi ----------------->
    <div class="form-konfirmasi">
        <h1 class="judul">Pemesanan Tiket</h1>
        <hr class="batas-judul-anime">
        <h2 class="info-kursi">Kursi yang Dipilih</h2>
        <table class="tabel-kursi">
            <tr>
                <th>No. Kursi</th>
            </tr>
            <?php foreach ($selected_seats as $seat_id): ?>
                <tr>
                    <td><?php echo htmlspecialchars($seatNumbers[$seat_id]); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2 class="harga">Total Harga</h2>
        <div class="info-harga">
            <p>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></p>
        </div>

        <h2>Data Pemesan</h2>
        <div class="data-pemesan">
            <form method="POST" action="process_booking.php" enctype="multipart/form-data">
                <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id); ?>">
                <input type="hidden" name="seats" value="<?= htmlspecialchars($selected_seats_json); ?>">
                <input type="hidden" name="total_price" value="<?= htmlspecialchars($total_price); ?>">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id); ?>">

                <label for="payment_method">Metode Pembayaran:</label>
                <select name="payment_method" id="payment_method" onchange="togglePaymentDetails()">
                    <option value="M-Banking">M-Banking</option>
                    <option value="Cash">Cash</option>
                </select>

                <div id="bank-details" style="display: block;"> <!-- Bagian ini akan disembunyikan jika metode pembayaran adalah "Cash" -->
                    <label for="nama_bank">Nama Bank:</label>
                    <select name="nama_bank" id="nama_bank">
                        <option value="Default">Default</option>
                        <option value="BCA">BCA</option>
                        <option value="BNI">BNI</option>
                        <option value="MANDIRI">MANDIRI</option>
                    </select>

                    <label for="no_rek_bioskop">Nomor Rekening Bank Bioskop:</label>
                    <select name="no_rek_bioskop" id="no_rek_bioskop">
                        <option value="Default">Default</option>
                        <option value="647913648149368">BCA - 647913648149368</option>
                        <option value="1234567890">BNI - 1234567890</option>
                        <option value="9876543210">MANDIRI - 9876543210</option>
                    </select>

                    <label for="no_rek_user">Nomor Rekening Anda:</label>
                    <input type="text" name="no_rek_user" id="no_rek_user" placeholder="Masukkan nomor rekening Anda" value="-">

                    <label for="bukti_pembayaran">Bukti Pembayaran:</label>
                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*">
                </div>

                <!-- Button Bayar akan tetap bisa ditekan meskipun Cash dipilih, tanpa perlu input lainnya -->
                <button type="submit" id="submit-button">Bayar</button>
            </form>
        </div>
    </div>

    <!----------------------- Footer -------------------------->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <img src="logo w.png" alt="Logo Perusahaan" class="footer-logo">
                <p>Cinepass merupakan website pemesanan tiket bioskop online yang digunakan untuk mempermudah dalam pemesanan tiket tanpa perlu antri</p>
            </div>
            <div class="footer-section links">
                <h3>Lainnya</h3>
                <ul>
                    <li><a href="#">Beranda</a></li>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Layanan</a></li>
                    <li><a href="#">Kontak</a></li>
                </ul>
            </div>
            <div class="footer-section social">
                <h3>Ikuti Kami</h3>
                <div class="social-links">
                    <a href="#"><img src="icon/facebook.png" alt="Facebook"></a>
                    <a href="#"><img src="icon/twitter.png" alt="Twitter"></a>
                    <a href="#"><img src="icon/instagram.png" alt="Instagram"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 Cinepass | Semua Hak Dilindungi
        </div>
    </footer>
</body>

</html>