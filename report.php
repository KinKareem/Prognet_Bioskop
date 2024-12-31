<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <!-- Sertakan file CSS -->
    <link rel="stylesheet" type="text/css" href="reportt.css">
</head>

<body>

    <header class="header">
        <h1>Dashboard Admin</h1>
    </header>

    <div class="wrapper">

        <div class="sidebar">
            <h2>Admin Menu</h2>
            <hr>
            <ul>
                <li><a href="tb_admin.php">Data User</a></li>
                <li><a href="movie.php">Movie</a></li>
                <li><a href="studio.php" class="active">Studio</a></li>
                <li><a href="jadwal.php">Jadwal</a></li>
                <li><a href="data_book.php">Booking</a></li>
                <li><a href="report.php">Report</a></li>
            </ul>
        </div>

        <!-- Content -->
        <div class="container">
            <h1>Data Report</h1>
            <hr>

            <!-- Form untuk memilih tanggal -->
            <form method="post" action="" class="form-tanggal">
                <label for="tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" name="tanggal_mulai" value="<?php echo $tanggal_mulai; ?>">
                <label for="tanggal_akhir">Tanggal Akhir:</label>
                <input type="date" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>">
                <input type="submit" value="Lihat Laporan">
            </form>

            <!-- Menampilkan Data Film Terlaris -->
            <?php
            // Koneksi ke database
            $servername = "localhost"; // Ganti dengan server Anda
            $username = "root"; // Ganti dengan username Anda
            $password = ""; // Ganti dengan password Anda
            $dbname = "db_bioskop";

            // Membuat koneksi
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Memeriksa koneksi
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Mendapatkan tanggal mulai dan tanggal akhir dari form (jika ada)
            $tanggal_mulai = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : date('Y-m-01');
            $tanggal_akhir = isset($_POST['tanggal_akhir']) ? $_POST['tanggal_akhir'] : date('Y-m-t');

            // Query untuk mendapatkan film terlaris dan total pendapatan dengan status Completed
            $sql = "
                SELECT 
                    m.judul, 
                    SUM(t.total_price) AS total_pendapatan, 
                    COUNT(b.booking_id) AS total_booking
                FROM 
                    tb_movie m
                JOIN 
                    tb_schedule s ON m.movie_id = s.movie_id
                JOIN 
                    tb_booking b ON s.schedule_id = b.schedule_id
                JOIN 
                    tb_transaksi t ON b.booking_id = t.booking_id
                WHERE 
                    t.tanggal_transaksi BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'
                    AND t.status = 'Completed'  -- Hanya menghitung transaksi yang statusnya Completed
                GROUP BY 
                    m.movie_id
                ORDER BY 
                    total_pendapatan DESC
            ";

            // Eksekusi query untuk film terlaris
            $result = $conn->query($sql);

            // Menyiapkan data untuk grafik
            $film_labels = [];
            $pendapatan_data = [];

            if ($result->num_rows > 0) {
                echo "<h1 class='film-laris'>Film Terlaris dari $tanggal_mulai hingga $tanggal_akhir</h1>";
                echo "<table class='tabel'>
                        <thead class='header-tabel'>
                            <tr class='kolom-tabel'>
                                <th>Judul Film</th>
                                <th>Total Pendapatan</th>
                                <th>Total Booking</th>
                            </tr>
                        </thead>
                        <tbody class='isi-data'>"; // Menambahkan elemen tbody untuk data tabel

                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='kolom-tabel'>
                            <td>" . $row['judul'] . "</td>
                            <td>Rp " . number_format($row['total_pendapatan'], 2, ',', '.') . "</td>
                            <td>" . $row['total_booking'] . "</td>
                        </tr>";

                    // Menambahkan data untuk grafik
                    $film_labels[] = $row['judul'];
                    $pendapatan_data[] = $row['total_pendapatan'];
                }

                echo "</tbody>
                </table>"; // Menutup elemen tbody dan tabel
            } else {
                echo "Tidak ada data untuk periode ini.";
            }
            // Query untuk mendapatkan total keseluruhan pendapatan
            $sql_total = "
                SELECT 
                    SUM(t.total_price) AS total_keseluruhan
                FROM 
                    tb_transaksi t
                WHERE 
                    t.tanggal_transaksi BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'
                    AND t.status = 'Completed'  -- Hanya menghitung transaksi yang statusnya Completed
            ";

            // Eksekusi query untuk total keseluruhan pendapatan
            $result_total = $conn->query($sql_total);
            $total_keseluruhan = 0;

            if ($result_total->num_rows > 0) {
                $row_total = $result_total->fetch_assoc();
                $total_keseluruhan = $row_total['total_keseluruhan'];
            }

            // Menampilkan total keseluruhan pendapatan
            echo "<h2 class='pendapatan'>Total Keseluruhan Pendapatan: Rp " . number_format($total_keseluruhan, 2, ',', '.') . "</h2>";

            // Menutup koneksi
            $conn->close();
            ?>

            <!-- Elemen Canvas untuk Grafik -->
            <canvas class="grafik" id="pendapatanChart" width="300" height="200"></canvas>
        </div>
    </div>

    <!-- Sertakan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Data untuk grafik
        const labels = <?php echo json_encode($film_labels); ?>;
        const data = {
            labels: labels,
            datasets: [{
                label: 'Total Pendapatan',
                data: <?php echo json_encode($pendapatan_data); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        // Konfigurasi grafik
        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        // Membuat grafik
        const pendapatanChart = new Chart(
            document.getElementById('pendapatanChart'),
            config
        );
    </script>
</body>

</html>