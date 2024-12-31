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
        1024: { // Tambahkan breakpoint baru
            slidesPerView: 3
        }
    }
});
