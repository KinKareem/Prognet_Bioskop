<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $schedule_id = $_POST['schedule_id'];
    $payment_method_id = $_POST['payment_method_id'];
    $total_harga = $_POST['total_harga'];

    // Simpan data pemesanan
    $sql_booking = "INSERT INTO tb_booking (user_id, schedule_id, total_harga, payment_method_id)
                    VALUES ('$user_id', '$schedule_id', '$total_harga', '$payment_method_id')";

    if ($conn->query($sql_booking) === TRUE) {
        $booking_id = $conn->insert_id; // Ambil ID pemesanan yang baru dibuat

        // Generate VA code jika metode pembayaran adalah M-Banking
        if ($payment_method_id == 'pm001') {
            $va_code = 'VA' . str_pad($booking_id, 6, '0', STR_PAD_LEFT);
            $conn->query("UPDATE tb_booking SET va_code = '$va_code' WHERE booking_id = '$booking_id'");
        }

        echo "Pemesanan berhasil! Booking ID: " . $booking_id;
        if (isset($va_code)) {
            echo " Kode VA: " . $va_code;
        }
    } else {
        echo "Error: " . $sql_booking . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>