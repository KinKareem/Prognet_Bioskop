<?php
include 'koneksi.php'; // Koneksi ke database

if (isset($_GET['id'])) {
    $transaksi_id = $_GET['id'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Ambil booking_id dari transaksi
        $getBookingId = "SELECT booking_id FROM tb_transaksi WHERE transaksi_id = ?";
        $stmt = $conn->prepare($getBookingId);
        $stmt->bind_param("s", $transaksi_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $booking = $result->fetch_assoc();
            $booking_id = $booking['booking_id'];

            // Hapus detail booking
            $deleteBookingDetail = "DELETE FROM tb_booking_detail WHERE booking_id = ?";
            $stmt = $conn->prepare($deleteBookingDetail);
            $stmt->bind_param("s", $booking_id);
            $stmt->execute();

            // Ubah status transaksi menjadi Failed
            $updateTransaksi = "UPDATE tb_transaksi SET status = 'Failed' WHERE transaksi_id = ?";
            $stmt = $conn->prepare($updateTransaksi);
            $stmt->bind_param("s", $transaksi_id);
            $stmt->execute();

            // Commit transaksi
            $conn->commit();
            // Redirect ke data_book.php dengan pesan sukses
            header("Location: data_book.php?message=Transaksi berhasil ditolak");
            exit; // Pastikan untuk keluar setelah redirect
        } else {
            throw new Exception("Transaksi tidak ditemukan.");
        }
    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $conn->rollback();
        // Redirect ke data_book.php dengan pesan kesalahan
        header("Location: data_book.php?message=Terjadi kesalahan saat menolak transaksi: " . urlencode($e->getMessage()));
        exit; // Pastikan untuk keluar setelah redirect
    }
}

// Menutup koneksi
$conn->close();
?>