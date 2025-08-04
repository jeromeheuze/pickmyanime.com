<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");
header("Pragma: no-cache");

include './includes/dbconnect.php';

function getAnimeByGenre($genre, $limit = 6) {
    global $DBcon;
    $stmt = $DBcon->prepare("SELECT title, poster, genres FROM anime_recommendations WHERE LOWER(genres) LIKE CONCAT('%', LOWER(?), '%') ORDER BY score DESC LIMIT ?");
    $stmt->bind_param("si", $genre, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$genreRows = [
    'Shounen' => getAnimeByGenre('Shounen'),
    'Slice of Life' => getAnimeByGenre('Slice of Life'),
    'Romance' => getAnimeByGenre('Romance'),
    'Fantasy' => getAnimeByGenre('Fantasy'),
    'Action' => getAnimeByGenre('Action'),
    'Sci-Fi' => getAnimeByGenre('Sci-Fi'),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="preload" as="image" href="/assets/img/pixlr-image-generator-6872d9e9175956f8cbdb3c10.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PickMyAnime – Mood-Based Anime Recommendations</title>
    <link rel="canonical" href="https://pickmyanime.com/" />
    <meta name="description" content="Discover your next anime based on your mood or vibe. PickMyAnime helps you find the perfect anime to watch, instantly." />
    <meta property="og:title" content="PickMyAnime – Mood-Based Anime Recommendations" />
    <meta property="og:description" content="Find the perfect anime to match your mood. Whether you're feeling sad, happy, curious, or nostalgic, PickMyAnime will guide your next watch." />
    <meta property="og:image" content="https://pickmyanime.com/assets/og-image.png" />
    <meta property="og:url" content="https://pickmyanime.com" />
    <meta name="twitter:card" content="summary_large_image" />
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16" />
    <script defer src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #111;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 4rem 1rem;
            text-align: center;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        p {
            max-width: 600px;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        button {
            background: #ff3b3b;
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #e32f2f;
        }
        .fade-in {
            opacity: 0;
            visibility: hidden;
            transition: opacity 1s ease-in-out, visibility 1s ease-in-out;
        }
        .fade-in.show {
            opacity: 1;
            visibility: visible;
        }
        @keyframes pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.25); }
            100% { transform: scale(1); }
        }

        .bump {
            animation: pop 0.4s ease;
        }
        .confetti-piece {
            position: fixed;
            width: 8px;
            height: 8px;
            background-color: #ffd700;
            opacity: 0.8;
            z-index: 9999;
            border-radius: 50%;
            animation: confetti-fall 1.5s linear forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(0) rotate(0);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
        .image-wrapper {
            position: relative;
            display: inline-block;
            border-radius: 16px;
            overflow: visible;
            z-index: 1;
        }

        .image-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 16px;
            background: radial-gradient(circle at center, #ff3b3b55, #00000000 70%);
            opacity: 0.3;
            filter: blur(12px);
            animation: softGlow 4s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes softGlow {
            0%, 100% {
                transform: scale(1);
                opacity: 0.25;
            }
            50% {
                transform: scale(1.02);
                opacity: 0.4;
            }
        }
    </style>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WFEVQE9XF8"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-WFEVQE9XF8');
    </script>
    <script src="https://analytics.ahrefs.com/analytics.js" data-key="4akxeutEU3/U31uZrbCycQ" async></script>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "PickMyAnime",
            "url": "https://pickmyanime.com",
            "description": "Find the perfect anime to match your mood. Whether you're feeling nostalgic or curious, PickMyAnime helps you decide what to watch next.",
            "publisher": {
                "@type": "Organization",
                "name": "PickMyAnime"
            }
        }
    </script>
</head>
<body style="background: radial-gradient(#222, #000);">
    <div class="image-wrapper">
        <img src="/assets/img/pixlr-image-generator-6872d9e9175956f8cbdb3c10.png"
             alt="Watch Anime Based on Your Mood"
             width="1024"
             height="768"
             loading="lazy"
             style="width: 50%; height: auto; border-radius: 16px;" />
    </div>
    <h1>What Anime Should I Watch Next?</h1>
    <p>PickMyAnime helps you find the perfect anime to watch based on your mood, vibe, or genre preferences. Whether you're feeling nostalgic, hyped, or in the mood for something chill — we've got you.</p>

    <button id="exciteBtn" onclick="trackExcitement()">Yes, I’m excited!</button>

    <p id="confirmationMessage" class="fade-in" style="margin-top: 1rem; color: #00ff99; font-weight: bold;">
        🎉 Thanks for your excitement! We'll keep you updated.
    </p>
    <p id="excitementBadge" style="margin-top: 2rem; font-size: 1rem; color: #ffaa33;">
        🔥 <span id="excitedCount" aria-live="polite">0</span> people are excited!
    </p>

    <?php //include './includes/random_recommendation.php'; ?>

    <style>
        .genre-section {
            width: 100%;
            max-width: 1000px;
            margin: 3rem auto;
            text-align: left;
        }
        .genre-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .genre-row {
            display: flex;
            overflow-x: auto;
            gap: 1rem;
            padding-bottom: 1rem;
        }
        .anime-card {
            flex: 0 0 auto;
            width: 160px;
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            color: #fff;
            transition: transform 0.2s ease;
        }

        .anime-card:hover {
            transform: scale(1.05);
        }
        .anime-card img {
            height: 240px;
            width: auto;
            display: block;
            margin: 0 auto;
            object-fit: cover;
            border-bottom: 1px solid #333;
        }
        .anime-card p {
            font-size: 0.85rem;
            padding: 0.5rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .lazy-blur {
            filter: blur(10px);
            transition: filter 0.6s ease;
            will-change: filter;
        }
        .lazy-blur.loaded {
            filter: blur(0);
        }
    </style>

    <?php foreach ($genreRows as $genre => $animeList): ?>
        <div class="genre-section">
            <h2 class="genre-title"><?= htmlspecialchars($genre) ?></h2>
            <div class="genre-row">
                <?php foreach ($animeList as $anime): ?>
                    <div class="anime-card">
                        <img
                                src="/assets/img/placeholder-blur.jpg"
                                data-src="<?= htmlspecialchars($anime['poster']) ?>"
                                alt="<?= htmlspecialchars($anime['title']) ?>"
                                class="lazy-blur"
                                loading="lazy">
                        <p><?= htmlspecialchars($anime['title']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>


    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const lazyImages = document.querySelectorAll("img.lazy-blur");

        const onImageLoad = (img) => {
            img.classList.add('loaded');
        };

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src;
                    if (src) {
                        img.src = src;
                        img.onload = () => onImageLoad(img);
                        obs.unobserve(img);
                    }
                }
            });
        });

        lazyImages.forEach(img => observer.observe(img));
    });
    window.onload = function () {
        let baseCount;
        fetch('/assets/get-clicks.php?t=' + Date.now())
            .then(res => res.json())
            .then(data => {
                baseCount = data.count;
                bumpCountDisplay();
            });
        const countEl = document.getElementById('excitedCount');
        const exciteBtn = document.getElementById('exciteBtn');
        const msg = document.getElementById('confirmationMessage');

        // Expose this so the button can call it
        window.trackExcitement = function () {
            // GA4 Event
            gtag('event', 'click', {
                event_category: 'Landing Page',
                event_label: 'Excitement Button',
                value: 1
            });

            if (exciteBtn) exciteBtn.style.display = 'none';
            if (msg) msg.classList.add('show');

            baseCount += 1;
            if (countEl) bumpCountDisplay();
            launchConfetti(); // 🎉

            fetch('/assets/track-click.php', {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.count) {
                        baseCount = data.count;
                        bumpCountDisplay();
                    }
                });

        };

        // Simulate increasing interest
        function updateCount() {
            const bump = Math.random() < 0.5 ? 0 : 1;
            baseCount += bump;
            if (countEl) bumpCountDisplay();
        }

        function bumpCountDisplay() {
            if (countEl) {
                countEl.textContent = baseCount;

                // Trigger bump animation
                countEl.classList.remove('bump'); // reset if already running
                void countEl.offsetWidth;         // force reflow
                countEl.classList.add('bump');
            }
        }

        function launchConfetti() {
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6 }
            });
        }

        //setInterval(updateCount, 15000);
    };
</script>

</body>
</html>
