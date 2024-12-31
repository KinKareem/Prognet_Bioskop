<?php
include 'koneksi.php';

// Cek apakah judul film ada di URL
if (!isset($_GET['judul'])) {
    die("Judul film tidak ditemukan.");
}

$judul = $_GET['judul'];

// Ambil data film dari database
$sql = "SELECT * FROM tb_movie WHERE judul = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $judul);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Data film tidak ditemukan.");
}

$movie = $result->fetch_assoc();

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan semua kunci ada dalam $_POST
    $old_judul = isset($_POST['old_judul']) ? $_POST['old_judul'] : '';
    $judul = isset($_POST['judul']) ? $_POST['judul'] : '';
    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
    $durasi = isset($_POST['durasi']) ? $_POST['durasi'] : 0; // Default ke 0 jika tidak ada
    $rating = isset($_POST['rating']) ? $_POST['rating'] : 0.0; // Default ke 0.0 jika tidak ada
    $sinopsis = isset($_POST['sinopsis']) ? $_POST['sinopsis'] : '';
    $trailer_url = isset($_POST['trailer_url']) ? $_POST['trailer_url'] : '';

    // Proses upload poster jika ada
    $poster = null;
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == UPLOAD_ERR_OK) {
        $poster = file_get_contents($_FILES['poster']['tmp_name']); // Ambil konten file
    } else {
        if (isset($_FILES['poster'])) {
            echo "Kesalahan upload file: " . $_FILES['poster']['error'] . "<br>";
        }
    }

    // Debugging: Tampilkan nilai parameter
    echo "Judul: $judul, Genre: $genre, Durasi: $durasi, Rating: $rating, Old Judul: $old_judul, Sinopsis: $sinopsis, Trailer URL: $trailer_url<br>";

    // Query untuk update data film
    $update_sql = "UPDATE tb_movie SET judul = ?, genre = ?, durasi = ?, rating = ?, poster = ?, sinopsis = ?, trailer_url = ? WHERE judul = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssisssss", $judul, $genre, $durasi, $rating, $poster, $sinopsis, $trailer_url, $old_judul);

    if ($update_stmt->execute()) {
        echo "Data film berhasil diperbarui.";
    } else {
        // Tambahkan debugging untuk menampilkan kesalahan
        echo "Terjadi kesalahan saat memperbarui data film: " . $update_stmt->error;
    }
} else {
    // Jika form belum disubmit, ambil data film dari database
    if (!isset($_GET['judul'])) {
        die("Judul film tidak ditemukan.");
    }

    $judul = $_GET['judul'];

    // Ambil data film dari database
    $sql = "SELECT * FROM tb_movie WHERE judul = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $judul);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Data film tidak ditemukan.");
    }

    $movie = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <link rel="stylesheet" href="edit_movie.css">
</head>

<body class="edit-movie-body">
    <header class="edit-movie-header">
        <h1 class="edit-movie-title">Edit Movie</h1>
    </header>

    <!-- tombol tambah data -->
    <div class="back">
        <a class="btn-back" href="movie.php">Kembali</a>
    </div>

    <form method="POST" action="edit_movie.php?judul=<?= urlencode($movie['judul']) ?>" enctype="multipart/form-data" class="edit-movie-form">
        <input type="hidden" name="old_judul" value="<?= $movie['judul'] ?>" class="edit-movie-hidden-input">

        <label for="judul" class="edit-movie-label">Judul:</label>
        <input type="text" name="judul" id="judul" value="<?= $movie['judul'] ?>" required class="edit-movie-input">

        <label for="genre" class="edit-movie-label">Genre:</label>
        <input type="text" name="genre" id="genre" value="<?= $movie['genre'] ?>" required class="edit-movie-input">

        <label for="durasi" class="edit-movie-label">Durasi (menit):</label>
        <input type="number" name="durasi" id="durasi" value="<?= $movie['durasi'] ?>" required class="edit-movie-input">

        <label for="rating" class="edit-movie-label">Rating:</label>
        <input type="text" name="rating" id="rating" value="<?= $movie['rating'] ?>" required class="edit-movie-input">

        <label for="poster" class="edit-movie-label">Poster (Upload):</label>
        <input type="file" name="poster" id="poster" accept="image/*" class="edit-movie-file-input">

        <button type="submit" class="edit-movie-button">Update Movie</button>
    </form>
</body>

</html>