<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pick a Mood – PickMyAnime</title>
    <meta name="description" content="Choose your current mood and get a personalized anime recommendation instantly." />
    <link rel="icon" href="/favicon-32x32.png" sizes="32x32" />

    <!-- Shoelace UI 2.20.1 -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.1/cdn/shoelace.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.20.1/cdn/themes/dark.css" />


    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom, #1a1a1a, #000000);
            color: #fff;
            text-align: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        h1 {
            font-size: 2.4rem;
            margin-bottom: 2rem;
        }
        .mood-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1.25rem;
            max-width: 720px;
            margin: 0 auto;
        }
        footer {
            margin-top: 3rem;
            font-size: 0.9rem;
            color: #aaa;
        }
    </style>
</head>
<body class="sl-theme-dark">
<h1>What’s Your Mood?</h1>

<div class="mood-grid">
    <sl-button href="/anime-recommendation/sad" variant="default" size="medium">😢 Sad</sl-button>
    <sl-button href="/anime-recommendation/chill" variant="default" size="medium">🧘 Chill</sl-button>
    <sl-button href="/anime-recommendation/romantic" variant="default" size="medium">❤️ Romantic</sl-button>
    <sl-button href="/anime-recommendation/hyped" variant="default" size="medium">🔥 Hyped</sl-button>
    <sl-button href="/anime-recommendation/curious" variant="default" size="medium">🤔 Curious</sl-button>
    <sl-button href="/anime-recommendation/angry" variant="default" size="medium">😡 Angry</sl-button>
    <sl-button href="/anime-recommendation/nostalgic" variant="default" size="medium">📼 Nostalgic</sl-button>
</div>

<footer>
    <p><a href="/" style="color:#aaa;">← Back to homepage</a></p>
</footer>
</body>
</html>
