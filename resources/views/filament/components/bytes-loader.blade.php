<div class="bytes-loader" id="bytes-loader">
    <div class="bytes-loader__container">
        <div class="bytes-loader-content">
            <div class="bytes-loader-logo">
                <img src="{{ asset('img/bytes/bytes-logo-write.png') }}" alt="Byte-s" />
            </div>
        </div>
    </div>
    <div class="bytes-loader__darkscreen"></div>
</div>

<style>
    .bytes-loader {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 9999;
        background-color: transparent;
        opacity: 1;
        visibility: visible;
        transition: all 0.5s ease-in-out;
    }

    .bytes-loader.loaded {
        opacity: 0;
        visibility: hidden;
    }

    .bytes-loader.is-animated .bytes-loader__darkscreen {
        animation: bytesBlockMove 1s 0.5s ease-in-out 1 both;
    }

    .bytes-loader.is-animated .bytes-loader__container {
        animation: bytesItemsOpacity 1s 0.5s ease-in-out 1 both;
    }

    .bytes-loader__container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #171d34;
    }

    .bytes-loader__container .bytes-loader-content {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translateX(-50%) translateY(-50%);
    }

    .bytes-loader__container .bytes-loader-content .bytes-loader-logo {
        width: 200px;
    }

    .bytes-loader__container .bytes-loader-content .bytes-loader-logo img {
        display: block;
        width: 100%;
        height: auto;
        animation: bytesFadeIn 0.5s 1 both;
    }

    .bytes-loader__darkscreen {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #111426;
        transform: translateY(102%);
    }

    @keyframes bytesBlockMove {
        0% { transform: translateY(102%); }
        35% { transform: translateY(0); }
        55% { transform: translateY(0); }
        100% { transform: translateY(-102%); }
    }

    @keyframes bytesItemsOpacity {
        0% { opacity: 1; }
        35% { opacity: 1; }
        60% { opacity: 0; }
        100% { opacity: 0; }
    }

    @keyframes bytesFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.getElementById('bytes-loader');
        if (!loader) return;

        requestAnimationFrame(() => {
            setTimeout(() => loader.classList.add('is-animated'), 50);
        });

        setTimeout(() => {
            loader.classList.add('loaded');
            loader.addEventListener('transitionend', () => loader.remove(), { once: true });
        }, 2200);
    });
</script>