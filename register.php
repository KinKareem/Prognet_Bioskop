<?php
include('koneksi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Simpan dengan hash MD5
    $no_telepon = $_POST['no_telepon'];

    // Menghasilkan ID pengguna
    $query = "SELECT COUNT(*) as count FROM tb_user";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $userCount = $row['count'] + 1; // Menghitung jumlah pengguna dan menambah 1
    $userId = 'usr' . str_pad($userCount, 3, '0', STR_PAD_LEFT); // Membuat ID dalam format usr001, usr002, dll.

    // Menyimpan data pengguna ke database
    $query = "INSERT INTO tb_user (user_id, nama, email, pass_user, no_telepon) VALUES ('$userId', '$nama', '$email', '$password', '$no_telepon')";
    if ($conn->query($query)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Registrasi gagal!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register</title>
    <link rel="stylesheet" href="registerr.css">
</head>

<body>
    <form action="register.php" method="POST">
        <h2>Register</h2>
        <label>Nama:</label>
        <input type="text" name="nama" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>No Telepon:</label>
        <input type="text" name="no_telepon" required>
        <button type="submit">Register</button>
    </form>
</body>

</html>