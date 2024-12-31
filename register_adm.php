<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $user_id = 'adm' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass_admin = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $no_telepon = $_POST['no_telepon'];
    $alamat = $_POST['alamat'];

    // Menggunakan prepared statement untuk memeriksa apakah email sudah terdaftar
    $checkEmailSql = "SELECT * FROM tb_admin WHERE email=?";
    $stmt = $conn->prepare($checkEmailSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email sudah terdaftar
        echo "Email sudah terdaftar. Silakan gunakan email lain.";
    } else {
        // Email belum terdaftar, lanjutkan dengan pendaftaran
        $sql = "INSERT INTO tb_admin (admin_id, nama, email, pass_admin, no_telepon, alamat) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $user_id, $nama, $email, $pass_admin, $no_telepon, $alamat);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Menutup statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
    <h2 class="text-center">Register</h2>
    <form method="POST" action="" id="registrationForm">
        <div class="form-group">
            <label for="nama">Nama:</label>
            <input type="text" class="form-control" id="nama" name="nama" required placeholder="Enter your name">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="show_confirm_password">
                <label class="form-check-label" for="show_confirm_password">Intip Password</label>
            </div>
            <small id="passwordWarning" class="text-danger" style="display: none;">Password dan konfirmasi password tidak cocok!</small>
        </div>
        <div class="form-group">
            <label for="no_telepon">No Telepon:</label>
            <input type="text" class="form-control" id="no_telepon" name="no_telepon" required placeholder="Enter your phone number">
        </div>
        <div class="form-group">
            <label for="alamat">Alamat:</label>
            <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Enter your address">
        </div>
        <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
    </form>
    <p class="text-center mt-3">Already have an account? <a href="login_adm.php" class="text-white">Login here</a></p>
</div>

<script>
    document.getElementById('show_confirm_password').addEventListener('change', function() {
        var confirmPasswordInput = document.getElementById('confirm_password');
        if (this.checked) {
            confirmPasswordInput.type = 'text'; // Ubah menjadi text untuk menampilkan password
        } else {
            confirmPasswordInput.type = 'password'; // Kembalikan ke password untuk menyembunyikan
        }
    });

    document.getElementById('confirm_password').addEventListener('input', function() {
        var password = document.getElementById('password').value;
        var confirmPassword = this.value;
        var warningMessage = document.getElementById('passwordWarning');

        if (confirmPassword !== password) {
            warningMessage.style.display = 'block'; // Tampilkan pesan peringatan
        } else {
            warningMessage.style.display = 'none'; // Sembunyikan pesan peringatan
        }
    });

    document.getElementById('registrationForm').addEventListener('submit', function(event) {
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('confirm_password').value;
        var warningMessage = document.getElementById('passwordWarning');

        if (confirmPassword !== password) {
            event.preventDefault(); // Mencegah pengiriman form
            warningMessage.style.display = 'block'; // Tampilkan pesan peringatan
        } else {
            warningMessage.style.display = 'none'; // Sembunyikan pesan peringatan
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>