<!-- http://localhost/App Bioskop/login_adm.php -->
 
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_bioskop";

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Menggunakan prepared statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("SELECT * FROM tb_admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $row['pass_admin'])) {
            // Menyimpan data admin dalam session
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['nama'] = $row['nama'];
            echo "Login successful! Welcome, " . htmlspecialchars($row['nama']);
            // Redirect atau load halaman lain setelah login berhasil
            header("Location: tb_admin.php"); // Uncomment jika ingin redirect
            exit();
        } else {
            echo "Invalid password. Please check your password.";
        }
    } else {
        echo "No admin found with that email.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, rgba(128, 0, 128, 1) 0%, rgba(75, 0, 130, 1) 100%);
            backdrop-filter: blur(10px);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 5px;
            color: white;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn-primary {
            background-color: rgba(0, 123, 255, 0.8);
            border: none;
        }
    </style>
</head>
<body>
<div class="card p-4" style="width: 25rem;">
    <h2 class="text-center">Login</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
    </form>
    <p class="text-center mt-3">Don't have an account? <a href="register_adm.php" class="text-white">Register here</a></p>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>