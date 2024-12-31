<?php
include 'koneksi.php';

// Fungsi untuk menghasilkan ID Film
function generateMovieId($conn)
{
    $query = "SELECT MAX(movie_id) AS last_id FROM tb_movie";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    $lastId = $row['last_id'];
    $newId = 1;

    if ($lastId) {
        // Mengambil angka dari ID terakhir dan menambahkannya
        $number = (int)substr($lastId, 3); // Ambil angka setelah "MOV"
        $newId = $number + 1;
    }

    // Menghasilkan ID baru dengan format "MOV-001"
    return "mov" . str_pad($newId, 3, '0', STR_PAD_LEFT);
}

// Proses penyimpanan data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = generateMovieId($conn); // Tambahkan baris ini
    $judul = $_POST['judul'];
    $genre = $_POST['genre'];
    $durasi = $_POST['durasi'];
    $rating = $_POST['rating'];
    $sinopsis = $_POST['sinopsis'];
    $trailer_url = $_POST['trailer_url'];
    $tanggal_tambah = date('Y-m-d H:i:s');

    // Proses upload poster
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $poster_tmp = $_FILES['poster']['tmp_name'];
        $poster = addslashes(file_get_contents($poster_tmp));

        // Query untuk memasukkan data ke tabel tb_movie
        $sql = "INSERT INTO tb_movie (movie_id, poster, judul, genre, durasi, rating, sinopsis, trailer_url, tanggal_tambah)
                VALUES ( '$movie_id','$poster', '$judul', '$genre', '$durasi', '$rating', '$sinopsis', '$trailer_url', '$tanggal_tambah')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Data berhasil ditambahkan!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error_message = "Terjadi kesalahan saat mengupload poster.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Film</title>
    <link rel="stylesheet" href="add.css">
</head>

<body>
    <header class="header">
        <h1>Tambah Data Film</h1>
    </header>

    <div class="container">
        <a href="movie.php" class="btn-back">Kembali</a>

        <?php if (isset($success_message)): ?>
            <div class="success-message"><?= $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
            <div class="error-message"><?= $error_message; ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">

            <div>
                <label for="poster">Poster</label>
                <input type="file" name="poster" id="poster" accept="image/*" required>
            </div>

            <div>
                <label for="judul">Judul</label>
                <input type="text" name="judul" id="judul" required>
            </div>

            <div>
                <label for="genre">Genre</label>
                <input type="text" name="genre" id="genre" required>
            </div>

            <div>
                <label for="durasi">Durasi (menit)</label>
                <input type="number" name="durasi" id="durasi" required>
            </div>

            <div>
                <label for="rating">Rating</label>
                <input type="number" step="0.1" name="rating" id="rating" required>
            </div>

            <div>
                <label for="sinopsis">Sinopsis</label>
                <textarea name="sinopsis" id="sinopsis" required></textarea>
            </div>

            <div>
                <label for="trailer_url">URL Trailer</label>
                <input type="url" name="trailer_url" id="trailer_url">
            </div>

            <button type="submit">Tambah Film</button>
        </form>
    </div>
</body>

</html>