<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tiket</title>
    <link rel="stylesheet" href="data_book.css"> <!-- Ganti dengan file CSS Anda -->
</head>

<body>

    <header>
        <h1>Data Tiket yang Dipesan</h1>
    </header>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Booking Detail ID</th>
                    <th>Studio Name</th>
                    <th>Movie Title</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Seat Number</th>
                    <th>Price per Ticket</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Koneksi ke database
                include 'koneksi.php'; // Pastikan file koneksi.php ada dan berfungsi

                // Query untuk mendapatkan data kursi yang dipesan berdasarkan booking_detail_id, studio, dan jadwal
                $sql = "
                    SELECT bd.booking_detail_id, s.nama_studio, m.judul AS movie_title, 
                           sc.tanggal, sc.waktu_mulai, sc.waktu_selesai, bd.seat_id AS seat_number, 
                           sc.harga_tiket AS price_per_ticket
                    FROM tb_booking_detail bd
                    JOIN tb_booking b ON bd.booking_id = b.booking_id
                    JOIN tb_transaksi t ON b.booking_id = t.booking_id
                    JOIN tb_schedule sc ON b.schedule_id = sc.schedule_id
                    JOIN tb_movie m ON sc.movie_id = m.movie_id
                    JOIN tb_studio s ON sc.studio_id = s.studio_id
                ";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Menampilkan data setiap kursi yang dipesan
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row['booking_detail_id'] . "</td>
                                <td>" . $row['nama_studio'] . "</td>
                                <td>" . $row['movie_title'] . "</td>
                                <td>" . $row['tanggal'] . "</td>
                                <td>" . date('H:i', strtotime($row['waktu_mulai'])) . "</td>
                                <td>" . date('H:i', strtotime($row['waktu_selesai'])) . "</td>
                                <td>" . $row['seat_number'] . "</td>
                                <td>" . number_format($row['price_per_ticket'], 2, ',', '.') . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data tiket yang ditemukan.</td></tr>";
                }

                // Menutup koneksi
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

</body>

</html>