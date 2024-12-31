<?php
include 'koneksi.php'; // Pastikan koneksi ke database

// Inisialisasi variabel
$schedule = null;

// Ambil schedule_id dari URL
$schedule_id = isset($_GET['schedule_id']) ? $_GET['schedule_id'] : null;

// Cek apakah schedule_id valid
if ($schedule_id) {
    // Ambil data jadwal dari database
    $sql = "SELECT * FROM tb_schedule WHERE schedule_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Data jadwal tidak ditemukan.");
    }

    $schedule = $result->fetch_assoc();
} else {
    die("Schedule ID tidak ditemukan.");
}

// Ambil daftar movie untuk dropdown
$movies_sql = "SELECT movie_id, judul FROM tb_movie";
$movies_result = $conn->query($movies_sql);

// Ambil daftar studio untuk dropdown
$studios_sql = "SELECT studio_id, nama_studio FROM tb_studio";
$studios_result = $conn->query($studios_sql);

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = $_POST['movie_id'];
    $studio_id = $_POST['studio_id'];
    $tanggal = $_POST['tanggal'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $harga_tiket = $_POST['harga_tiket'];

    // Query untuk update data jadwal
    $update_sql = "UPDATE tb_schedule SET movie_id = ?, studio_id = ?, tanggal = ?, waktu_mulai = ?, waktu_selesai = ?, harga_tiket = ? WHERE schedule_id = ?";
    $update_stmt = $conn->prepare($update_sql);

    if ($update_stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameter
    $update_stmt->bind_param("sssssis", $movie_id, $studio_id, $tanggal, $waktu_mulai, $waktu_selesai, $harga_tiket, $schedule_id);

    // Eksekusi query
    if ($update_stmt->execute()) {
        // Redirect ke halaman jadwal setelah berhasil diupdate
        header("Location: jadwal.php?message=Data berhasil diperbarui");
        exit();
    } else {
        echo "Terjadi kesalahan saat memperbarui data jadwal: " . $update_stmt->error;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal</title>
    <link rel="stylesheet" href="edit_jadwall.css">
</head>

<body class="edit-jadwal-body">

    <header class="edit-jadwal-header">
        <h1>Edit Jadwal</h1>
    </header>

    <!-- tombol tambah data -->
    <div class="back">
        <a class="btn-back" href="jadwal.php">Kembali</a>
    </div>

    <form method="POST" class="edit-jadwal-form">
        <input type="hidden" name="schedule_id" value="<?php echo isset($schedule['schedule_id']) ? $schedule['schedule_id'] : ''; ?>">

        <label for="movie_id" class="edit-jadwal-label">Movie:</label>
        <select name="movie_id" class="edit-jadwal-select" required>
            <option value="">Pilih Movie</option>
            <?php
            // Tampilkan daftar movie di dropdown
            while ($movie = $movies_result->fetch_assoc()) {
                $selected = ($movie['movie_id'] == $schedule['movie_id']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($movie['movie_id']) . "' $selected>" . htmlspecialchars($movie['judul']) . "</option>";
            }
            ?>
        </select>

        <label for="studio_id" class="edit-jadwal-label">Studio:</label>
        <select name="studio_id" class="edit-jadwal-select" required>
            <option value="">Pilih Studio</option>
            <?php
            while ($studio = $studios_result->fetch_assoc()) {
                $selected = ($studio['studio_id'] == $schedule['studio_id']) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($studio['studio_id']) . "' $selected>" . htmlspecialchars($studio['nama_studio']) . "</option>";
            }
            ?>
        </select>

        <label for="tanggal" class="edit-jadwal-label">Tanggal:</label>
        <input type="date" name="tanggal" class="edit-jadwal-input" value="<?php echo isset($schedule['tanggal']) ? $schedule['tanggal'] : ''; ?>" required>

        <label for="waktu_mulai" class="edit-jadwal-label">Waktu Mulai:</label>
        <input type="time" name="waktu_mulai" class="edit-jadwal-input" value="<?php echo isset($schedule['waktu_mulai']) ? $schedule['waktu_mulai'] : ''; ?>" required>

        <label for="waktu_selesai" class="edit-jadwal-label">Waktu Selesai:</label>
        <input type="time" name="waktu_selesai" class="edit-jadwal-input" value="<?php echo isset($schedule['waktu_selesai']) ? $schedule['waktu_selesai'] : ''; ?>">

        <label for="harga_tiket" class="edit-jadwal-label">Harga Tiket:</label>
        <input type="number" name="harga_tiket" class="edit-jadwal-input" value="<?php echo isset($schedule['harga_tiket']) ? $schedule['harga_tiket'] : ''; ?>" required>

        <button type="submit" class="edit-jadwal-button">Update Jadwal</button>
    </form>

</body>

</html>