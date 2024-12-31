<?php
include 'koneksi.php'; // Koneksi ke database

if (isset($_GET['id'])) {
    $transaksi_id = $_GET['id'];

    // Ubah status transaksi menjadi approved
    $sql = "UPDATE tb_transaksi SET status = 'Completed' WHERE transaksi_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $transaksi_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: data_book.php?message=Transaksi berhasil disetujui");
    } else {
        header("Location: data_book.php?message=Gagal menyetujui transaksi");
    }
}

// Menutup koneksi
$conn->close();
?>