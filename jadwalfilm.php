<?php
include('koneksi.php');
session_start();

// Ambil ID film dari parameter URL
$movie_id = isset($_GET['movie_id']) ? $_GET['movie_id'] : '';

// Query untuk mengambil detail film
$movieQuery = "SELECT * FROM tb_movie WHERE movie_id = '$movie_id'";
$movieResult = $conn->query($movieQuery);
$movie = $movieResult->fetch_assoc();

// Query untuk mengambil jadwal film
$scheduleQuery = "SELECT * FROM tb_schedule WHERE movie_id = '$movie_id'";
$schedules = $conn->query($scheduleQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($movie['judul']); ?> - Jadwal Film</title>
    <link rel="stylesheet" href="jadwalfilm.css">
</head>

<body>

    <!-------------- header home ---------------->
    <header class="header_home">
        <img src="logo w.png" class="logo_home" alt="">
    </header>

    <!---------------- Movie detail --------------->
    <div class="movie-detail">
        <?php
        // Cek apakah gambar disimpan sebagai binary atau URL
        $posterSource = '';
        if (filter_var($movie['poster'], FILTER_VALIDATE_URL)) {
            // Jika `poster` adalah URL
            $posterSource = htmlspecialchars($movie['poster']);
        } else {
            // Jika `poster` adalah binary (BLOB), ubah menjadi base64
            $posterSource = 'data:image/jpeg;base64,' . base64_encode($movie['poster']);
        }
        ?>
        <img src="<?= $posterSource; ?>" alt="<?= htmlspecialchars($movie['judul']); ?>" class="movie-poster">
        <div class="movie-info">
            <h1><?= htmlspecialchars($movie['judul']); ?></h1>
            <p><strong>Genre:</strong> <?= htmlspecialchars($movie['genre']); ?></p>
            <p><strong>Durasi:</strong> <?= htmlspecialchars($movie['durasi']); ?> menit</p>
            <p><strong>Rating:</strong> <?= htmlspecialchars($movie['rating']); ?></p>
            <p><strong>Sinopsis:</strong> <?= htmlspecialchars($movie['sinopsis']); ?></p>
        </div>
    </div>

    <!----------------------- Pilih Jadwal ------------------------>
    <h2 class="judul-jadwal">Jadwal Film</h2>
    <hr class="batas-jadwal">

    <?php if ($schedules->num_rows > 0): ?>
        <div class="schedule-list">
            <?php while ($schedule = $schedules->fetch_assoc()): ?>
                <?php
                // Menghitung hari dari tanggal
                $tanggal = $schedule['tanggal'];
                $dayName = date('l', strtotime($tanggal)); // Mengambil nama hari
                ?>
                <div class="schedule-card">
                    <h3 class="hari"><strong><?= htmlspecialchars($dayName); ?></strong></h3>
                    <hr class="batas">
                    <div class="schedule-info">
                        <p><strong>Tanggal: </strong> <?= htmlspecialchars($tanggal); ?></p>
                        <p><strong>Waktu: </strong> <?= htmlspecialchars($schedule['waktu_mulai']); ?> - <?= htmlspecialchars($schedule['waktu_selesai']); ?></p>
                        <p><strong>Harga Tiket:</strong> Rp <?= htmlspecialchars($schedule['harga_tiket']); ?></p> <!-- Menampilkan harga tiket -->
                        <a href="tiket.php?schedule_id=<?= htmlspecialchars($schedule['schedule_id']); ?>" class="btn-book">Booking Kursi</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="no-fiilm">Tidak ada jadwal untuk film ini.</p>
    <?php endif; ?>


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

</body>

</html>