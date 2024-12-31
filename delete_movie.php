<?php
include 'koneksi.php'; // Pastikan koneksi ke database

// Cek apakah parameter judul ada
if (isset($_GET['judul'])) {
    $judul = $_GET['judul'];

    // Query untuk menghapus data film
    $delete_sql = "DELETE FROM tb_movie WHERE judul = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("s", $judul);

    if ($delete_stmt->execute()) {
        echo "Data film berhasil dihapus.";
    } else {
        echo "Terjadi kesalahan saat menghapus data film: " . $delete_stmt->error;
    }
} else {
    echo "Judul film tidak ditemukan.";
}

// Redirect kembali ke movie.php setelah proses
header("Location: movie.php");
exit();
?>