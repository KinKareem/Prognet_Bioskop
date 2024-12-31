<?php
class cbook {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function processBooking($data) {
        // Validasi data
        if (!$this->validateData($data)) {
            return "Data tidak lengkap. Silakan kembali dan coba lagi.";
        }

        // Ambil data dari POST
        $schedule_id = $data['schedule_id'];
        $selected_seats_json = $data['seats'];
        $total_price = $data['total_price'];
        $user_id = $data['user_id'];
        $payment_method_id = $data['payment_method_id'];
        $nama_bank = $data['nama_bank'];
        $no_rek = $data['no_rek'];

        // Hitung jumlah kursi yang dipesan
        $selected_seats = json_decode($selected_seats_json, true);
        $jumlah_kursi = count($selected_seats);

        // Simpan data pemesanan ke dalam tabel tb_booking
        $booking_id = $this->saveBooking($user_id, $schedule_id, $jumlah_kursi);
        if (!$booking_id) {
            return "Error saving booking.";
        }

        // Simpan detail kursi yang dipesan
        foreach ($selected_seats as $seat_id) {
            if (!$this->saveBookingDetail($booking_id, $seat_id, $payment_method_id)) {
                return "Error saving booking detail.";
            }
        }

        // Simpan transaksi
        foreach ($selected_seats as $seat_id) {
            if (!$this->saveTransaction($booking_id, $user_id, $payment_method_id, $nama_bank, $no_rek, $total_price)) {
                return "Error saving transaction.";
            }
        }

        return "Pemesanan berhasil! ID Pemesanan Anda: " . htmlspecialchars($booking_id);
    }

    private function validateData($data) {
        return isset($data['schedule_id'], $data['seats'], $data['user_id'], $data['payment_method_id'], $data['nama_bank'], $data['no_rek'], $data['total_price']);
    }

    private function saveBooking($user_id, $schedule_id, $jumlah_kursi) {
        $query = "INSERT INTO tb_booking (user_id, schedule_id, jumlah_kursi) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $user_id, $schedule_id, $jumlah_kursi);
        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return false;
    }

    private function saveBookingDetail($booking_id, $seat_id, $payment_method_id) {
        $booking_detail_id = "bd" . str_pad($booking_id, 7, "0", STR_PAD_LEFT);
        $query = "INSERT INTO tb_booking_detail (booking_detail_id, booking_id, seat_id, payment_method_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $booking_detail_id, $booking_id, $seat_id, $payment_method_id);
        return $stmt->execute();
    }

    private function saveTransaction($booking_id, $user_id, $payment_method_id, $nama_bank, $no_rek, $total_price) {
        $booking_detail_id = "bd" . str_pad($booking_id, 7, "0", STR_PAD_LEFT);
        $query = "INSERT INTO tb_transaksi (booking_detail_id, user_id, payment_method_id, nama_bank, no_rek, total_price, status, tanggal_transaksi) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssds", $booking_detail_id, $user_id, $payment_method_id, $nama_bank, $no_rek, $total_price);
        return $stmt->execute();
    }
}
?>