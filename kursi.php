<?php
// Koneksi ke database
include 'koneksi.php';

// Mengambil schedule_id dari request JSON
$data = json_decode(file_get_contents('php://input'), true);
$schedule_id = $data['schedule_id'];

// Query untuk mengambil kursi berdasarkan schedule_id
$query = "SELECT seat_id, nomor_kursi, status FROM kursi WHERE schedule_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$result = $stmt->get_result();

// Array untuk menampung data kursi
$seats = [];

while ($row = $result->fetch_assoc()) {
    $seats[] = $row; // Menambahkan data kursi ke array
}

// Menutup koneksi
$stmt->close();
$conn->close();

// Mengembalikan data kursi dalam format JSON
echo json_encode($seats);
?>

?>