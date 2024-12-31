<?php
include 'koneksi.php'; // Pastikan koneksi ke database

// Cek apakah schedule_id ada di URL
if (isset($_GET['schedule_id'])) {
    $schedule_id = $_GET['schedule_id'];
    echo "Schedule ID: " . htmlspecialchars($schedule_id); // Debugging

    // Query untuk menghapus data jadwal
    $sql = "DELETE FROM tb_schedule WHERE schedule_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $schedule_id);

    if ($stmt->execute()) {
        // Redirect ke halaman jadwal setelah berhasil dihapus
        header("Location: jadwal.php?message=Data berhasil dihapus");
        exit();
    } else {
        echo "Terjadi kesalahan saat menghapus data: " . $stmt->error;
    }
} else {
    echo "Schedule ID tidak ditemukan.";
}
?>