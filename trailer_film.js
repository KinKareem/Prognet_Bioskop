function playTrailer(url) {
    const trailerContainer = document.getElementById('trailer-container');
    const trailerVideo = document.getElementById('trailer-video');

    // Embed YouTube video with autoplay
    trailerVideo.src = url.replace('watch?v=', 'embed/') + '?autoplay=1';
    trailerContainer.style.display = 'flex';
}

function closeTrailer() {
    const trailerContainer = document.getElementById('trailer-container');
    const trailerVideo = document.getElementById('trailer-video');

    trailerContainer.style.display = 'none';
    trailerVideo.src = ''; // Stop the video
}
