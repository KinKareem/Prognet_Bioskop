<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = $_POST['schedule_id'];
    $user_id = 'usr001'; // Ganti dengan ID pengguna yang sesuai
    $total_harga = 30000; // Ganti dengan total harga yang sesuai

    // Ambil metode pembayaran
    $payment_methods = $conn->query("SELECT * FROM tb_payment_methods");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Pembayaran</title>
</head>
<body>
    <h1>Pilih Metode Pembayaran</h1>
    <form action="process.php" method="POST">
        <input type="hidden" name="schedule_id" value="<?= $schedule_id; ?>">
        <input type="hidden" name="user_id" value="<?= $user_id; ?>">
        <input type="hidden" name="total_harga" value="<?= $total_harga; ?>">
        
        <select name="payment_method_id" required>
            <option value="">Pilih Metode Pembayaran</option>
            <?php while ($method = $payment_methods->fetch_assoc()): ?>
                <option value="<?= $method['payment_method_id']; ?>">
                    <?= $method['method_name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        
        <button type="submit">Konfirmasi Pembayaran</button>
    </form>
</body>
</html>
<?php
} else {
    echo "Invalid request.";
}
?>