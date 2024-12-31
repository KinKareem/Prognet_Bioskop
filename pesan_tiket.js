// Step 1: Pilih Film
$("#film").change(function() {
    let filmId = $(this).val();
    if (filmId) {
        $("#step2").removeClass("hidden");
        $.ajax({
            url: "process.php",
            type: "POST",
            data: {
                action: "get_schedule", // Pastikan ini benar
                film_id: filmId
            },
            success: function(data) {
                $("#jadwal").html(data);
            }
        });
    } else {
        $("#step2, #step3, #step4").addClass("hidden");
    }
});

// Step 2: Pilih Jadwal
$("#jadwal").change(function() {
    let scheduleId = $(this).val();
    if (scheduleId) {
        $("#step3").removeClass("hidden");
        $.ajax({
            url: "process.php",
            type: "POST",
            data: {
                action: "get_seats", // Pastikan ini benar
                schedule_id: scheduleId
            },
            success: function(data) {
                $("#seat-selection").html(data);
            }
        });
    } else {
        $("#step3, #step4").addClass("hidden");
    }
});

// Step 3: Pilih Kursi
$(document).on("click", ".seat.available", function () {
    $(this).toggleClass("selected");
    updateSelectedSeats();
});

// Fungsi untuk memperbarui kursi yang dipilih dan total harga
function updateSelectedSeats() {
    let selectedSeats = $(".seat.selected").map(function () {
        return $(this).data("seat-id");
    }).get();

    let hargaTiket = parseInt($("#jadwal").find(":selected").data("harga"));
    let totalHarga = selectedSeats.length * hargaTiket;

    $("#selected-seats").text(selectedSeats.join(", "));
    $("#total-harga").text(totalHarga);

    // Masukkan data ke dalam form untuk dikirim
    $("#seats").val(selectedSeats.join(","));
    $("#total_price").val(totalHarga);
}
