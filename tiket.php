<?php
include('koneksi.php'); // Pastikan koneksi ke database sudah ada
session_start();

// Hapus semua data sesi yang berkaitan dengan pemesanan sebelumnya
unset($_SESSION['selected_seats']);
unset($_SESSION['schedule_id']);
unset($_SESSION['total_price']);
unset($_SESSION['booking_id']); // Hapus booking_id jika ada

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Anda belum login. Silakan login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit();
}

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
$user_phone = $_SESSION['user_phone'];

// Ambil ID jadwal dari parameter URL
$schedule_id = isset($_GET['schedule_id']) ? $_GET['schedule_id'] : '';

// Query untuk mengambil detail jadwal termasuk harga tiket dan movie_id
$scheduleQuery = "SELECT * FROM tb_schedule WHERE schedule_id = ?";
$stmt = $conn->prepare($scheduleQuery);
$stmt->bind_param("s", $schedule_id);
$stmt->execute();
$scheduleResult = $stmt->get_result();

if ($scheduleResult->num_rows === 0) {
    die("Jadwal tidak ditemukan."); // Error jika jadwal tidak ditemukan
}

$schedule = $scheduleResult->fetch_assoc();

// Ambil movie_id dari jadwal
$movie_id = $schedule['movie_id'];

// Query untuk mengambil nama film dari tb_movie
$movieQuery = "SELECT judul FROM tb_movie WHERE movie_id = ?";
$stmt = $conn->prepare($movieQuery);
$stmt->bind_param("s", $movie_id);
$stmt->execute();
$movieResult = $stmt->get_result();

if ($movieResult->num_rows === 0) {
    die("Film tidak ditemukan."); // Error jika film tidak ditemukan
}

$movie = $movieResult->fetch_assoc();
$movie_title = $movie['judul']; // Mengambil judul film

// Ambil harga tiket dari jadwal
$ticket_price = $schedule['harga_tiket']; // Mengambil harga tiket dari jadwal

// Ambil informasi kursi berdasarkan studio
$studio_id = $schedule['studio_id'];
$seatQuery = "SELECT * FROM tb_seat WHERE studio_id = ?";
$stmt = $conn->prepare($seatQuery);
$stmt->bind_param("s", $studio_id);
$stmt->execute();
$seats = $stmt->get_result();

// Ambil informasi booking untuk menentukan kursi yang terisi
$bookedSeatsQuery = "SELECT seat_id FROM tb_booking_detail WHERE booking_id IN (SELECT booking_id FROM tb_booking WHERE schedule_id = ?)";
$stmt = $conn->prepare($bookedSeatsQuery);
$stmt->bind_param("s", $schedule_id);
$stmt->execute();
$bookings = $stmt->get_result();

$bookedSeats = [];
while ($booking = $bookings->fetch_assoc()) {
    $bookedSeats[] = $booking['seat_id'];
}

// Setelah kursi dipilih dan tombol konfirmasi diklik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats = $_POST['seats']; // Ambil kursi yang dipilih dari form
    $_SESSION['selected_seats'] = $selected_seats; // Simpan ke session

    // Ambil schedule_id dari form
    $schedule_id = $_POST['schedule_id']; // Pastikan ini ada di form
    $_SESSION['schedule_id'] = $schedule_id; // Simpan ke session

    // Misalkan booking_id dihasilkan di sini
    $_SESSION['booking_id'] = uniqid(); // Contoh ID booking yang unik

    // Redirect ke booking.php
    header("Location: booking.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Kursi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="tikett.css">
</head>

<body>

    <!-------------- header home ---------------->
    <header class="header_home">
        <img src="logo w.png" class="logo_home" alt="">
    </header>

    <!------------- Pilih Kursi ---------->
    <div class="container">
        <h1><?= htmlspecialchars($movie_title); ?></h1> <!-- Menampilkan judul film -->
        <div class="legend">
            <div class="available"><span></span>Available</div>
            <div class="booked"><span></span>Booked</div>
            <div class="selected"><span></span>Selected</div>
        </div>
        <img src="icon/Desain_tanpa_judul__1_-removebg-preview.png" class="layar-bioskop">
        <!-- urutan pertama -->
        <div class="atur-kursi">
            <div class="seats">
                <div class="baris-kursi">
                    <?php
                    $counter = 0; // Inisialisasi penghitung kursi
                    // Tampilkan kursi
                    while ($seat = $seats->fetch_assoc()):
                        if ($counter >= 10) break; // Hentikan loop jika sudah mencapai 10 kursi
                        $seatClass = in_array($seat['seat_id'], $bookedSeats) ? 'booked' : 'available';
                    ?>
                        <div class="seat <?= $seatClass; ?>" data-seat-id="<?= htmlspecialchars($seat['seat_id']); ?>">
                            <?= htmlspecialchars($seat['nomor_kursi']); ?>
                        </div>
                    <?php
                    endwhile;
                    ?>
                </div>
            </div>
            <!-- urutan kedua -->
            <br>
            <div class="seats">
                <div class="baris-kursi">
                    <?php
                    $counter = 0; // Inisialisasi penghitung kursi
                    // Tampilkan kursi
                    while ($seat = $seats->fetch_assoc()):
                        if ($counter >= 10) break; // Hentikan loop jika sudah mencapai 10 kursi
                        $seatClass = in_array($seat['seat_id'], $bookedSeats) ? 'booked' : 'available';
                    ?>
                        <div class="seat <?= $seatClass; ?>" data-seat-id="<?= htmlspecialchars($seat['seat_id']); ?>">
                            <?= htmlspecialchars($seat['nomor_kursi']); ?>
                        </div>
                    <?php
                    endwhile;
                    ?>
                </div>
            </div>
            <!-- urutan ketiga -->
            <div class="seats">
                <div class="d-flex flex-wrap">
                    <?php
                    $counter = 0; // Inisialisasi penghitung kursi
                    // Tampilkan kursi
                    while ($seat = $seats->fetch_assoc()):
                        if ($counter >= 10) break; // Hentikan loop jika sudah mencapai 10 kursi
                        $seatClass = in_array($seat['seat_id'], $bookedSeats) ? 'booked' : 'available';
                    ?>
                        <div class="seat <?= $seatClass; ?>" data-seat-id="<?= htmlspecialchars($seat['seat_id']); ?>">
                            <?= htmlspecialchars($seat['nomor_kursi']); ?>
                        </div>
                    <?php
                    endwhile;
                    ?>
                </div>
            </div>
            <!-- urutan keempat -->
            <div class="seats">
                <div class="baris-kursi">
                    <?php
                    $counter = 0; // Inisialisasi penghitung kursi
                    // Tampilkan kursi
                    while ($seat = $seats->fetch_assoc()):
                        if ($counter >= 10) break; // Hentikan loop jika sudah mencapai 10 kursi
                        $seatClass = in_array($seat['seat_id'], $bookedSeats) ? 'booked' : 'available';
                    ?>
                        <div class="seat <?= $seatClass; ?>" data-seat-id="<?= htmlspecialchars($seat['seat_id']); ?>">
                            <?= htmlspecialchars($seat['nomor_kursi']); ?>
                        </div>
                    <?php
                        $counter++; // Tingkatkan penghitung kursi
                    endwhile;
                    ?>
                </div>
            </div>
        </div>

        <div class="hitung-harga">
            <p>Harga per tiket: <strong id="ticket-price"><?= htmlspecialchars($ticket_price); ?></strong></p>
            <p>Total Harga: <strong id="total-price">0</strong></p>
        </div>
        <form id="booking-form" method="POST" action="booking.php" style="display: none;">
            <input type="hidden" name="seats" id="seats-input">
            <input type="hidden" name="total_price" id="total-price-input">
            <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id); ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($user_name); ?>">
            <input type="hidden" name="phone" value="<?= htmlspecialchars($user_phone); ?>">
        </form>
        <button id="confirm-booking" class="btn btn-dark" disabled>Konfirmasi Pemesanan</button>
    </div>

    <!----------------------- Footer ------------------------->

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


    <!-- JS -->
    <script>
        const seats = document.querySelectorAll('.seat');
        const totalPriceElement = document.getElementById('total-price');
        const ticketPrice = parseFloat(document.getElementById('ticket-price').innerText);
        let selectedSeats = [];

        // Hapus data dari Local Storage saat halaman dimuat
        window.onload = function() {
            localStorage.removeItem('selectedSeats'); // Menghapus kursi yang dipilih
            localStorage.removeItem('totalPrice'); // Menghapus total harga

            const savedSeats = JSON.parse(localStorage.getItem('selectedSeats'));
            const savedPrice = localStorage.getItem('totalPrice');
            if (savedSeats) {
                selectedSeats = savedSeats;
                updateTotalPrice(); // Update total harga
                selectedSeats.forEach(seatId => {
                    document.querySelector(`.seat[data-seat-id="${seatId}"]`).classList.add('selected');
                });
            }
            if (savedPrice) {
                totalPriceElement.innerText = savedPrice; // Tampilkan total harga
            }
        };

        // Menambahkan event listener untuk setiap kursi
        seats.forEach(seat => {
            seat.addEventListener('click', function() {
                if (this.classList.contains('available')) {
                    this.classList.toggle('selected'); // Toggle kelas 'selected'
                    const seatId = this.dataset.seatId; // Ambil ID kursi

                    // Tambahkan atau hapus kursi dari array selectedSeats
                    if (selectedSeats.includes(seatId)) {
                        selectedSeats = selectedSeats.filter(id => id !== seatId);
                    } else {
                        selectedSeats.push(seatId);
                    }

                    updateTotalPrice(); // Update total harga
                }
            });
        });

        // Fungsi untuk memperbarui total harga
        function updateTotalPrice() {
            const totalPrice = selectedSeats.length * ticketPrice;
            totalPriceElement.innerText = totalPrice.toFixed(2); // Tampilkan total harga

            // Simpan ke Local Storage
            localStorage.setItem('selectedSeats', JSON.stringify(selectedSeats));
            localStorage.setItem('totalPrice', totalPrice);

            // Enable the confirm booking button if there are selected seats
            document.getElementById('confirm-booking').disabled = selectedSeats.length === 0; // Aktifkan tombol jika ada kursi yang dipilih }
        }
        // Event listener untuk tombol konfirmasi pemesanan
        document.getElementById('confirm-booking').addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah pengiriman form default

            // Pastikan ada kursi yang dipilih sebelum melanjutkan
            if (selectedSeats.length === 0) {
                alert("Silakan pilih kursi terlebih dahulu."); // Tampilkan pesan jika tidak ada kursi yang dipilih
                return; // Hentikan eksekusi jika tidak ada kursi yang dipilih
            }

            const selectedSeatsJson = JSON.stringify(selectedSeats); // Konversi kursi yang dipilih ke format JSON
            const totalPrice = selectedSeats.length * ticketPrice;

            // Isi input tersembunyi dengan data yang diperlukan
            document.getElementById('seats-input').value = selectedSeatsJson;
            document.getElementById('total-price-input').value = totalPrice;

            // Kirim form ke booking.php
            document.getElementById('booking-form').submit();
        });
    </script>
</body>

</html>