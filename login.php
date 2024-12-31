<?php
include('koneksi.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Pastikan Anda menggunakan hashing yang aman

    // Menggunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE email = ? AND pass_user = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Store user information in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['nama']; // Menyimpan nama pengguna ke dalam sesi
        $_SESSION['user_email'] = $user['email']; // Menyimpan email pengguna
        $_SESSION['user_phone'] = $user['no_telepon']; // Menyimpan nomor telepon pengguna
        // Add any other user information you want to store in the session
        $_SESSION['user_address'] = $user['alamat']; // Menyimpan alamat pengguna (jika ada)

        header('Location: home.php'); // Redirect ke halaman home
        exit(); // Pastikan untuk keluar setelah redirect
    } else {
        echo "<script>alert('Email atau password salah!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
    <link rel="stylesheet" href="loginn.css">
</head>

<body>
    <form action="login.php" method="POST">
        <h2>Login</h2>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
        <p class="text-center mt-3">Don't have an account? <a href="register.php" class="text-white">Register here</a></p>
        <p class="text-center mt-3"> <a href="home.php" class="text-white">Login as guest</a></p>
    </form>
</body>

</html>