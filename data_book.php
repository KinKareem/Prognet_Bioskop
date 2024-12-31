<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Transaksi</title>
    <link rel="stylesheet" href="tb_adminn.css">
    <style>
        /* CSS untuk modal */
        .modal {
            display: none;
            /* Tersembunyi secara default */
            position: fixed;
            /* Tetap di tempat */
            z-index: 1;
            /* Di atas elemen lain */
            left: 0;
            top: 0;
            width: 100%;
            /* Lebar penuh */
            height: 100%;
            /* Tinggi penuh */
            overflow: auto;
            /* Aktifkan scroll jika diperlukan */
            background-color: rgb(0, 0, 0);
            /* Warna latar belakang */
            background-color: rgba(0, 0, 0, 0.4);
            /* Warna latar belakang dengan transparansi */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            /* 15% dari atas dan tengah */
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            /* Lebar modal */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <header class="header">
        <h1>Data Booking</h1>
    </header>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Menu</h2>
            <hr>
            <ul>
                <li><a href="tb_admin.php">Data User</a></li>
                <li><a href="movie.php">Movie</a></li>
                <li><a href="studio.php">Studio</a></li>
                <li><a href="jadwal.php">Jadwal</a></li>
                <li><a href="data_book.php">Booking</a></li>
                <li><a href="report.php">Report</a></li>
            </ul>
        </div>

        <!-- Content -->
        <div class="container">
            <h1>Data Booking</h1>
            <hr>

            <!-- Form Search dan Filter -->
            <form method="GET" class="filter-form">
                <!--  atur berapa data yang ditampilkan -->
                <div class="atur-jumlah">
                    <div class="data-halaman">
                        <label for="records_per_page">Tampilkan:</label>
                        <select name="records_per_page" id="records_per_page" onchange="this.form.submit()">
                            <option value="5" <?= isset($_GET['records_per_page']) && $_GET['records_per_page'] == '5' ? 'selected' : '' ?>>5</option>
                            <option value="10" <?= isset($_GET['records_per_page']) && $_GET['records_per_page'] == '10' ? 'selected' : '' ?>>10</option>
                        </select>
                        <label for="records_per_page">Baris</label>
                    </div>

                    <!-- Input Search -->
                    <div class="cari-data">
                        <input type="text" name="search" placeholder="Cari ID Booking" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                        <button type="submit">Cari</button>
                    </div>
                </div>
            </form>

            <!-- Atur Tabel -->
            <table class="tabel">
                <thead class="header-tabel">
                    <tr class="kolom-tabel">
                        <th>Transaction ID</th>
                        <th>Booking ID</th>
                        <th>Payment Method</th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Transaction Date</th>
                        <th>Payment Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody class="isi-data">
                    <?php
                    include 'koneksi.php'; // Pastikan file koneksi ada

                    // Pagination
                    $limit = 5; // Jumlah baris per halaman
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
                    $offset = ($page - 1) * $limit; // Offset untuk query

                    // Ambil parameter pencarian jika ada
                    $search = isset($_GET['search']) ? $_GET['search'] : null;

                    // Query untuk menampilkan data transaksi
                    if ($search) {
                        // Jika ada pencarian
                        $sql = "SELECT transaksi_id, booking_id, payment_method, nama_bank, no_rek, total_price, status, tanggal_transaksi, bukti_pembayaran 
                            FROM tb_transaksi 
                            WHERE transaksi_id LIKE ? 
                            ORDER BY transaksi_id DESC 
                            LIMIT ? OFFSET ?";
                        $stmt = $conn->prepare($sql);
                        $searchParam = '%' . $search . '%';
                        $stmt->bind_param("sii", $searchParam, $limit, $offset);
                    } else {
                        // Jika tidak ada pencarian
                        $sql = "SELECT transaksi_id, booking_id, payment_method, nama_bank, no_rek, total_price, status, tanggal_transaksi, bukti_pembayaran 
                            FROM tb_transaksi 
                            ORDER BY transaksi_id DESC 
                            LIMIT ? OFFSET ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $limit, $offset);
                    }

                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='kolom-tabel'>
                                <td>" . $row['transaksi_id'] . "</td>
                                <td>" . $row['booking_id'] . "</td>
                                <td>" . $row['payment_method'] . "</td>
                                <td>" . $row['nama_bank'] . "</td>
                                <td>" . $row['no_rek'] . "</td>
                                <td>" . number_format($row['total_price'], 2, ',', '.') . "</td>
                                <td>" . $row['status'] . "</td>
                                <td>" . $row['tanggal_transaksi'] . "</td>
                                <td>";
                            if ($row['bukti_pembayaran']) {
                                echo "<a href='#' class='btn-view' onclick='openModal(\"" . base64_encode($row['bukti_pembayaran']) . "\")'>View</a>";
                            } else {
                                echo "No proof";
                            }
                            echo "</td>
                                <td>
                                    <a href='detail_transaction.php?id=" . $row['transaksi_id'] . "' class='btn-detail'>Detail</a>
                                    <a href='approve_transaction.php?id=" . $row['transaksi_id'] . "' class='btn-approve'>Approve</a>
                                    <a href='reject_transaction.php?id=" . $row['transaksi_id'] . "' class='btn-reject' onclick='return confirm(\"Yakin ingin menolak transaksi ini?\")'>Reject</a>
                                </td>
                              </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>Tidak ada data transaksi yang ditemukan.</td></tr>";
                    }

                    // Menghitung total baris untuk pagination
                    if ($search) {
                        $sqlCount = "SELECT COUNT(*) as total FROM tb_transaksi WHERE transaksi_id LIKE ?";
                        $stmtCount = $conn->prepare($sqlCount);
                        $stmtCount->bind_param("s", $searchParam);
                    } else {
                        $sqlCount = "SELECT COUNT(*) as total FROM tb_transaksi";
                        $stmtCount = $conn->prepare($sqlCount);
                    }

                    $stmtCount->execute();
                    $resultCount = $stmtCount->get_result();
                    $totalRows = $resultCount->fetch_assoc()['total'];
                    $totalPages = ceil($totalRows / $limit); // Total halaman
                    ?>
                </tbody>
            </table>

            <!-- Navigasi Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>">&laquo; Prev</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>


        <!-- Modal untuk Menampilkan Bukti Pembayaran -->
        <div id="paymentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Bukti Pembayaran</h2>
                <img id="paymentProofImage" src="" alt="Bukti Pembayaran" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>

    <script>
        function openModal(buktiPembayaran) {
            document.getElementById("paymentProofImage").src = 'data:image/jpeg;base64,' + buktiPembayaran; // Atur src gambar
            document.getElementById("paymentModal").style.display = "block"; // Tampilkan modal
        }

        function closeModal() {
            document.getElementById("paymentModal").style.display = "none"; // Sembunyikan modal
        }

        // Tutup modal jika pengguna mengklik di luar modal
        window.onclick = function(event) {
            var modal = document.getElementById("paymentModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>

</html>