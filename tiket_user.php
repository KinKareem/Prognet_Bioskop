<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
// Tentukan jumlah data per halaman
$items_per_page = 5;

// Menentukan halaman saat ini
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Mendapatkan query pencarian jika ada
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk menghitung total data, dengan filter pencarian jika ada
$total_sql = "SELECT COUNT(t.transaksi_id) AS total FROM tb_transaksi t
              JOIN tb_booking b ON t.booking_id = b.booking_id
              JOIN tb_user u ON b.user_id = u.user_id
              JOIN tb_schedule s ON b.schedule_id = s.schedule_id
              JOIN tb_movie m ON s.movie_id = m.movie_id
              WHERE u.user_id = ? AND m.judul LIKE ?";
$total_stmt = $conn->prepare($total_sql);
$search_param = "%" . $search . "%"; // Menambahkan wildcard untuk pencarian
$total_stmt->bind_param("ss", $user_id, $search_param);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $items_per_page);

// Query untuk mengambil semua history pemesanan dengan LIMIT, OFFSET, dan filter pencarian
$sql = "SELECT t.transaksi_id, t.total_price, t.status, 
               u.nama AS user_nama, 
               m.judul AS film_judul, 
               s.schedule_id,  
               s.tanggal, s.waktu_mulai, 
               GROUP_CONCAT(seat.nomor_kursi SEPARATOR ', ') AS kursi_pilih
        FROM tb_transaksi t
        LEFT JOIN tb_booking b ON t.booking_id = b.booking_id
        LEFT JOIN tb_user u ON b.user_id = u.user_id
        LEFT JOIN tb_schedule s ON b.schedule_id = s.schedule_id
        LEFT JOIN tb_movie m ON s.movie_id = m.movie_id
        LEFT JOIN tb_booking_detail bd ON b.booking_id = bd.booking_id
        LEFT JOIN tb_seat seat ON bd.seat_id = seat.seat_id
        WHERE u.user_id = ? AND m.judul LIKE ?
        GROUP BY t.transaksi_id, t.total_price, t.status, u.nama, m.judul, s.schedule_id, s.tanggal, s.waktu_mulai
        ORDER BY t.tanggal_transaksi DESC
        LIMIT ? OFFSET ?"; // Menggunakan LIMIT dan OFFSET

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $user_id, $search_param, $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tiket yang Dipesan</title>
    <link rel="stylesheet" href="tiket_user.css">
</head>

<body>
    <!-- Header -->
    <header class="header_home">
        <img src="logo w.png" class="logo_home" alt="">
        <h1>Welcome, <?= htmlspecialchars($username); ?></h1>
        <nav class="header_link">
            <a href="home.php" class="nextHome">Home</a>
            <a href="about us.html" class="nextData">About Us</a>
            <a href="tiket_user.php" class="nextTiket">My Ticket</a>
            <?php if ($username === 'Guest'): ?>
                <button id="loginBtn">Login</button>
            <?php else: ?>
                <button id="logoutBtn">Logout</button>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Main Tiket -->
    <h1 class="judul-tiket">Daftar Tiket yang Dipesan
        <hr class="batas-judul-anime">
    </h1>

    <!-- Pencarian -->
    <form method="get" action="tiket_user.php" class="search-form">
        <input type="text" name="search" placeholder="Cari Film..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Cari</button>
    </form>

    <table class="ticket-table">
        <thead class="ticket-header">
            <tr>
                <th>Transaction ID</th>
                <th>Film</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody class="ticket-body">
            <?php
            if ($result->num_rows > 0) {
                // Menampilkan data setiap pemesanan
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='ticket-row'>
                        <td>" . htmlspecialchars($row['transaksi_id']) . "</td>
                        <td>" . htmlspecialchars($row['film_judul']) . "</td>
                        <td>" . htmlspecialchars($row['tanggal']) . "</td>
                        <td>" . htmlspecialchars($row['waktu_mulai']) . "</td>
                        <td>Rp " . number_format($row['total_price'], 2, ',', '.') . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>";
                    // Cek status transaksi
                    if ($row['status'] === 'Completed') {
                        echo "<a href='download_transaksi.php?transaksi_id=" . htmlspecialchars($row['transaksi_id']) . "' class='download-btn'>Download Tiket</a>";
                    } else {
                        echo "Tiket tidak tersedia untuk diunduh.";
                    }
                    echo "</td>
                      </tr>";
                }
            } else {
                echo "<tr><td colspan='7'> Tidak ada riwayat pemesanan yang ditemukan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($page = 1; $page <= $total_pages; $page++): ?>
            <a href="?page=<?php echo $page; ?>&search=<?php echo htmlspecialchars($search); ?>" <?php echo $page == $current_page ? 'class="active"' : ''; ?>>
                <?php echo $page; ?>
            </a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next &raquo;</a>
        <?php endif; ?>
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


    <!-- JS Script -->
    <script src="homee.js"></script>
</body>

</html>

<?php
// Menutup koneksi
$conn->close();
?>