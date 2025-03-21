<?php

?>
<div class="body">
    <div class="radar">
        <div class="radar__dot"></div>
        <div class="radar__dot"></div>
        <div class="radar__dot"></div>
    </div>
</div>

<style>
.body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: linear-gradient(#252525, #0c0c0c);
}

.radar {
    position: relative;
    width: 60vmin;
    height: 60vmin;
    border: 2.5vmin solid transparent;
    border-radius: 50%;
    box-sizing: border-box;
    overflow: hidden;
    filter: drop-shadow(1vmin 1vmin 1vmin rgba(0, 0, 0, 0.4));
    background:
        repeating-radial-gradient(transparent,
            transparent 4.5%,
            rgba(80, 255, 0, 0.35) 5%,
            transparent 5.5%) content-box,

        linear-gradient(transparent 49.7%,
            rgba(80, 255, 0, 0.2) 49.9%,
            rgba(80, 255, 0, 0.2) 50.1%,
            transparent 50.3%) content-box,

        linear-gradient(to right,
            transparent 49.7%,
            rgba(80, 255, 0, 0.2) 49.9%,
            rgba(80, 255, 0, 0.2) 50.1%,
            transparent 50.3%) content-box,

        radial-gradient(#002500, #000500) content-box,
        linear-gradient(to bottom right, #ccc, #666) border-box;
}

.radar::after {
    content: "";
    position: absolute;
    inset: 0;
    background-image: conic-gradient(transparent 90%, rgba(80, 255, 0, 0.35));
    border-radius: 50%;
    box-shadow: inset 0 0 2vmin rgba(0, 0, 0, 0.9);
    animation: spin 2s linear infinite;
}

.radar__dot {
    position: absolute;
    width: 3%;
    height: 3%;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: blink 2s ease-out infinite;
}

.radar__dot:first-of-type {
    top: 24%;
    left: 76%;
    animation-delay: 0.25s;
}

.radar__dot:nth-of-type(2) {
    top: 80%;
    left: 20%;
    animation-delay: 1.25s;
}

.radar__dot:last-of-type {
    top: 36%;
    left: 36%;
    animation-delay: 1.75s;
}




@keyframes spin {
    to {
        transform: rotate(1turn);
    }
}

@keyframes blink {

    2%,
    20% {
        background-color: rgba(80, 255, 0, 0.85);
        box-shadow: 0 0 1vmin rgba(80, 255, 0, 0.6);
    }

    90% {
        background-color: transparent;
    }
}
</style>