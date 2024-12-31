// script.js

// Add click event to toggle seat selection
document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', () => {
      if (!seat.classList.contains('booked')) {
        seat.classList.toggle('selected');
        updateTicketCount();
      }
    });
  });
  
  // Update ticket count
  function updateTicketCount() {
    const selectedSeats = document.querySelectorAll('.seat.selected').length;
    document.querySelector('.footer span b').textContent = selectedSeats;
  }