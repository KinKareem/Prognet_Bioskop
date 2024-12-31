<?php
include 'koneksi.php';

// Fungsi untuk menghasilkan ID jadwal
function generateScheduleId($conn)
{
    $query = "SELECT MAX(schedule_id) AS last_id FROM tb_schedule";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    $lastId = $row['last_id'];
    $newId = 1;

    if ($lastId) {
        // Mengambil angka dari ID terakhir dan menambahkannya
        $number = (int)substr($lastId, 4);
        $newId = $number + 1;
    }

    // Menghasilkan ID baru dengan format "SCH-001"
    return "scd" . str_pad($newId, 3, '0', STR_PAD_LEFT);
}

// Proses penyimpanan data jika form disubmit
// Proses penyimpanan data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = generateScheduleId($conn); // Tambahkan baris ini
    $movie_id = $_POST['movie_id'];
    $studio_id = $_POST['studio_id'];
    $tanggal = $_POST['tanggal'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $harga_tiket = $_POST['harga_tiket'];

    // Validasi apakah ada jadwal yang sama
    $check_sql = "SELECT * FROM tb_schedule
                  WHERE movie_id = ? AND studio_id = ? AND tanggal = ? AND waktu_mulai = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iiss", $movie_id, $studio_id, $tanggal, $waktu_mulai);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Jika ada jadwal yang sama
        $error_message = "Jadwal tayang untuk film ini sudah ada pada tanggal dan waktu yang sama di studio yang dipilih.";
    } else {
        // Jika tidak ada jadwal yang sama, simpan data baru
        $sql = "INSERT INTO tb_schedule (schedule_id, movie_id, studio_id, tanggal, waktu_mulai, waktu_selesai, harga_tiket)
                VALUES ('$schedule_id', '$movie_id', '$studio_id', '$tanggal', '$waktu_mulai', '$waktu_selesai', '$harga_tiket')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Data berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Tayang</title>
    <link rel="stylesheet" href="add.css">
</head>

<body>
    <header class="header">
        <h1>Tambah Jadwal Tayang</h1>
    </header>

    <div class="container">
        <a href="jadwal.php" class="btn-back">Kembali</a>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="error-message"><?= $error_message; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div>
                <label for="movie_id">Film</label>
                <select name="movie_id" id="movie_id" required>
                    <option value="">Pilih Film</option>
                    <?php
                    $movies = $conn->query("SELECT movie_id, judul FROM tb_movie");
                    while ($movie = $movies->fetch_assoc()) {
                        echo "<option value='" . $movie['movie_id'] . "'>" . $movie['judul'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="studio_id">Studio</label>
                <select name="studio_id" id="studio_id" required>
                    <option value="">Pilih Studio</option>
                    <?php
                    $studios = $conn->query("SELECT studio_id, nama_studio FROM tb_studio");
                    while ($studio = $studios->fetch_assoc()) {
                        echo "<option value='" . $studio['studio_id'] . "'>" . $studio['nama_studio'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required>
            </div>

            <div>
                <label for="waktu_mulai">Waktu Mulai</label>
                <select name="waktu_mulai" id="waktu_mulai" required>
                    <option value="">Pilih Waktu</option>
                    <option value="12:00">12:00</option>
                    <option value="16:00">16:00</option>
                    <option value="19:00">19:00</option>
                </select>
            </div>

            <div>
                <label for="waktu_selesai">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" id="waktu_selesai" required>
            </div>

            <div>
                <label for="harga_tiket">Harga Tiket</label>
                <input type="text" name="harga_tiket" id="harga_tiket" required>
            </div>

            <button type="submit">Tambah Jadwal</button>
        </form>
    </div>
</body>

</html>