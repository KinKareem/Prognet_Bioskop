// download_tiket.js

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.download-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            // Ambil data dari atribut data-* yang ada di tombol
            var bookingId = this.getAttribute('data-id');
            var judulFilm = this.getAttribute('data-film');
            var genre = this.getAttribute('data-genre');
            var durasi = this.getAttribute('data-durasi');
            var rating = this.getAttribute('data-rating');
            var studio = this.getAttribute('data-studio');
            var tanggal = this.getAttribute('data-tanggal');
            var waktuMulai = this.getAttribute('data-waktu_mulai');
            var waktuSelesai = this.getAttribute('data-waktu_selesai');
            var hargaTiket = this.getAttribute('data-harga_tiket');
            var kursi = this.getAttribute('data-kursi');

            // Ambil gambar template tiket (misalnya 'template_tiket.png')
            var img = new Image();
            img.src = 'icon/tiket.png';  // Ganti dengan path gambar template tiket Anda

            img.onload = function () {
                // Membuat canvas untuk menggambar gambar dan teks
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');

                // Tentukan ukuran canvas sesuai dengan gambar template
                canvas.width = img.width;
                canvas.height = img.height;

                // Gambar gambar template ke dalam canvas
                ctx.drawImage(img, 0, 0);

                // Menambahkan teks di atas gambar (tiket)
                ctx.font = '18px Arial';
                ctx.fillStyle = 'black';
                ctx.fillText('ID Booking: ' + bookingId, 20, 40);
                ctx.fillText('Judul Film: ' + judulFilm, 20, 70);
                ctx.fillText('Genre: ' + genre, 20, 100);
                ctx.fillText('Durasi: ' + durasi + ' menit', 20, 130);
                ctx.fillText('Rating: ' + rating, 20, 160);
                ctx.fillText('Studio: ' + studio, 20, 190);
                ctx.fillText('Tanggal: ' + tanggal, 20, 220);
                ctx.fillText('Waktu Mulai: ' + waktuMulai, 20, 250);
                ctx.fillText('Waktu Selesai: ' + waktuSelesai, 20, 280);
                ctx.fillText('Harga Tiket: ' + hargaTiket, 20, 310);
                ctx.fillText('Nomor Kursi: ' + kursi, 20, 340);

                // Buat file gambar dari canvas dan unduh
                var link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');  // Mengonversi canvas menjadi URL gambar
                link.download = "tiket_" + bookingId + ".png";  // Nama file berdasarkan ID booking
                link.click();
            };
        });
    });
});
