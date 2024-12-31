// Warna-warna yang akan dipakai
const colors = ['red', 'blue', 'green', 'orange', 'purple'];
let colorIndex = 0;

// Pilih semua link dengan class `header_link`
const headerLinks = document.querySelectorAll('.header_link a');

headerLinks.forEach(link => {
    link.addEventListener('mouseover', () => {
        // Atur warna hover berdasarkan index
        document.documentElement.style.setProperty('--hover-color', colors[colorIndex]);

        // Update index warna untuk iterasi berikutnya
        colorIndex = (colorIndex + 1) % colors.length; // Reset ke 0 setelah warna terakhir
    });
});

let lastScrollPosition = 0;
const header = document.querySelector('.header_home');
let isScrolling;

// Menampilkan header saat halaman pertama kali dimuat
header.classList.add('visible');

// Deteksi aktivitas scroll
window.addEventListener('scroll', () => {
    const currentScrollPosition = window.pageYOffset;

    // Jika pengguna scroll ke bawah, sembunyikan header
    if (currentScrollPosition > lastScrollPosition) {
        header.classList.remove('visible');
    } else {
        // Jika pengguna scroll ke atas, tampilkan header
        header.classList.add('visible');
    }

    // Set posisi scroll terakhir
    lastScrollPosition = currentScrollPosition;

    // Deteksi apakah pengguna berhenti scroll
    clearTimeout(isScrolling);
    isScrolling = setTimeout(() => {
        // Tampilkan header jika pengguna berhenti scroll
        header.classList.add('visible');
    }, 200); // Waktu jeda setelah berhenti scroll (200ms)
});

// Trailer
new Swiper('.game-wrapper', {
    loop: true,
    spaceBetween: 30,

    // pagination
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: true
    },

    // Navigation arrows
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    // breakpoints
    breakpoints: {
        0: {
            slidesPerView: 1
        },
        768: {
            slidesPerView: 2
        },
    }
});
