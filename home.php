<!-- http://localhost/App Bioskop/home.php -->

<?php
session_start(); // Memulai sesi
include 'koneksi.php';

// Mengambil data film terbaru
$latestMovies = $conn->query("SELECT * FROM tb_movie ORDER BY tanggal_tambah DESC");

// Cek apakah pengguna sudah login
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- File Css -->
    <link rel="stylesheet" href="home.css">

    <!-- File Swiper JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Link icon -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Movie Booking</title>
</head>

<body>

    <!-- header home -->
    <header class="header_home">
        <img src="logo w.png" class="logo_home" alt="">
        <h1>Welcome, <?= htmlspecialchars($username); ?></h1>
        <nav class="header_link">
            <a href="home.php" class="nextHome">Home</a>
            <a href="tiket_user.php?username=<?= urlencode($username); ?>">My Ticket</a>
            <a href="about us.html" class="nextData">About Us</a>
            <?php if ($username === 'Guest'): ?>
                <button id="loginBtn">Login</button>
            <?php else: ?>
                <button id="logoutBtn">Logout</button>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Buat memilih movie -->

    <div class="div_anime">
        <h2>NOW SHOWING
            <hr class="batas-judul-anime">
        </h2>
        <div class="horizontal_scroll">
            <div class="isi_anime">
                <?php while ($movie = $latestMovies->fetch_assoc()): ?>
                    <?php
                    // Periksa apakah poster adalah URL atau binary
                    $poster_src = filter_var($movie['poster'], FILTER_VALIDATE_URL)
                        ? $movie['poster'] // Jika URL, gunakan langsung
                        : 'data:image/jpeg;base64,' . base64_encode($movie['poster']); // Jika binary, konversi ke base64
                    ?>
                    <div class="anime">
                        <img src="<?= htmlspecialchars($poster_src); ?>" alt="<?= htmlspecialchars($movie['judul']); ?>">
                        <h2><?= htmlspecialchars($movie['judul']); ?></h2>
                        <p>Genre: <?= htmlspecialchars($movie['genre']); ?></p>
                        <p>Durasi: <?= htmlspecialchars($movie['durasi']); ?> menit</p>
                        <a href="jadwalfilm.php?movie_id=<?= htmlspecialchars($movie['movie_id']); ?>">Lihat Jadwal</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="div_game">
        <h2>VIDEO & TRAILERS
            <hr class="batas-judul-spotlight">
        </h2>
        <div class="game-container swiper">
            <div class="game-wrapper">
                <ul class="game-list swiper-wrapper">
                    <?php
                    // Ambil data dari tabel tb_movie
                    $sql = "SELECT movie_id, judul, trailer_url FROM tb_movie ORDER BY RAND()";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Ekstrak ID video YouTube dari URL
                            preg_match('/v=([^&]+)/', $row['trailer_url'], $matches);
                            $youtube_id = $matches[1] ?? '';

                            // URL thumbnail YouTube
                            $thumbnail_src = "https://img.youtube.com/vi/$youtube_id/hqdefault.jpg";
                    ?>
                            <li class="game-item swiper-slide">
                                <div class="video-card">
                                    <a href="javascript:void(0)" class="video-link" onclick="playTrailer('<?= $row['trailer_url'] ?>')">
                                        <div class="thumbnail-container">
                                            <img class="thumbnail" src="<?= $thumbnail_src ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                                            <div>
                                                <img src="icon/icon_play.png" alt="" class="play-icon">
                                            </div>
                                        </div>
                                    </a>
                                    <p class="video-title"><?= htmlspecialchars($row['judul']) ?></p>
                                </div>
                            </li>
                    <?php
                        }
                    } else {
                        echo "<p>No trailers available.</p>";
                    }
                    ?>
                </ul>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </div>

        <!-- Div untuk video trailer -->
        <div id="trailer-container" class="trailer-container" style="display: none;">
            <div class="trailer-overlay" onclick="closeTrailer()"></div>
            <div class="trailer-content">
                <iframe id="trailer-video" src="" frameborder="0" allowfullscreen></iframe>
                <button class="close-button" onclick="closeTrailer()">&times;</button>
            </div>
        </div>
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


    <script>
        document.getElementById('logoutBtn')?.addEventListener('click', function() {
            // Logika untuk logout
            alert('You have logged out.');
            // Redirect to logout page
            window.location.href = 'logout.php'; // Pastikan Anda memiliki file logout.php untuk menangani logout
        });

        document.getElementById('loginBtn')?.addEventListener('click', function() {
            // Redirect to login page
            window.location.href = 'login.php';
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="homee.js"> </script>
    <script src="slidemovie.js"> </script>
    <script src="trailer_film.js"> </script>
</body>

</html>