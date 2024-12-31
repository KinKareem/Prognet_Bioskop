<?php
// Menampilkan notifikasi jika ada
if (isset($_GET['message'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['message']) . "</div>";
}
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Jadwal Tayang</title>
    <link rel="stylesheet" href="tb_adminn.css">
</head>

<body>

    <header class="header">
        <h1>Dashboard Admin</h1>
    </header>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Menu</h2>
            <hr>
            <ul>
                <li><a href="tb_admin.php">Data User</a></li>
                <li><a href="movie.php">Movie</a></li>
                <li><a href="studio.php">Studio</a></li>
                <li><a href="jadwal.php">Jadwal</a></li>
                <li><a href="data_book.php">Booking</a></li>
                <li><a href="report.php">Report</a></li>
            </ul>
        </div>

        <!-- Content -->
        <div class="container">
            <h1>Jadwal Tayang</h1>
            <hr>
            <!-- tombol tambah data -->
            <div class="tambah">
                <a class="btn-add" href="add_jadwal.php">Tambah Jadwal</a>
            </div>

            <!-- Form Search dan Filter -->
            <form method="GET" class="filter-form">
                <!--  atur berapa data yang ditampilkan -->
                <div class="atur-jumlah">
                    <!-- Dropdown jumlah data -->
                    <div class="data-halaman">
                        <label for="records_per_page">Tampilkan:</label>
                        <select name="records_per_page" id="records_per_page" onchange="this.form.submit()">
                            <option value="5" <?= isset($_GET['records_per_page']) && $_GET['records_per_page'] == '5' ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= isset($_GET['records_per_page']) && $_GET['records_per_page'] == '10' ? 'selected' : '' ?>>10</option>
                        </select>
                        <label for="records_per_page">Baris</label>
                    </div>

                    <!-- Input Search -->
                    <div class="cari-data">
                        <input type="text" name="search" placeholder="Cari Judul Film" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                        <button type="submit">Cari</button>
                    </div>
                </div>
            </form>

            <table class="tabel">
                <thead class="header-tabel">
                    <tr class="kolom-tabel">
                        <th>Schedule ID</th>
                        <th>Nama Film</th>
                        <th>Nama Studio</th>
                        <th>Tanggal</th>
                        <th>Waktu Mulai</th>
                        <th>Harga Tiket</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody class="isi-data">
                    <?php
                    // Default jumlah data per halaman
                    $records_per_page = isset($_GET['records_per_page']) ? (int)$_GET['records_per_page'] : 5;

                    // Halaman saat ini
                    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                    // Offset untuk query
                    $offset = ($current_page - 1) * $records_per_page;

                    // Search filter
                    $search = isset($_GET['search']) ? $_GET['search'] : '';

                    // Query untuk menghitung total data
                    $total_data_query = "
                        SELECT COUNT(*) AS total
                        FROM tb_schedule
                        JOIN tb_movie ON tb_schedule.movie_id = tb_movie.movie_id
                        JOIN tb_studio ON tb_schedule.studio_id = tb_studio.studio_id
                        WHERE tb_movie.judul LIKE '%$search%'
                    ";
                    $total_data_result = $conn->query($total_data_query);
                    $total_data = $total_data_result->fetch_assoc()['total'];

                    // Query untuk mendapatkan data dengan JOIN
                    $sql = "
                        SELECT tb_schedule.schedule_id, tb_movie.judul AS nama_film, tb_studio.nama_studio, tb_schedule.tanggal, tb_schedule.waktu_mulai, tb_schedule.harga_tiket
                        FROM tb_schedule
                        JOIN tb_movie ON tb_schedule.movie_id = tb_movie.movie_id
                        JOIN tb_studio ON tb_schedule.studio_id = tb_studio.studio_id
                        WHERE tb_movie.judul LIKE '%$search%'
                        LIMIT $records_per_page OFFSET $offset
                    ";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='kolom-tabel'>";
                            echo "<td>" . $row['schedule_id'] . "</td>";
                            echo "<td>" . $row['nama_film'] . "</td>";
                            echo "<td>" . $row['nama_studio'] . "</td>";
                            echo "<td>" . $row['tanggal'] . "</td>";
                            echo "<td>" . date('H.i', strtotime($row['waktu_mulai'])) . "</td>"; // Format waktu menjadi Jam.Menit
                            echo "<td>" . $row['harga_tiket'] . "</td>";
                            echo "<td>
                                    <a href='edit_schedule.php?schedule_id=" . urlencode($row['schedule_id']) . "' class='btn-edit'>Edit</a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>Tidak ada data yang ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Paginasi -->
            <div class="pagination">
                <?php
                $total_pages = ceil($total_data / $records_per_page);
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<a href='?page=$i&records_per_page=$records_per_page&search=$search' class='" . ($i == $current_page ? "active" : "") . "'>$i</a>";
                }
                ?>
            </div>

        </div>
    </div>

</body>

</html>