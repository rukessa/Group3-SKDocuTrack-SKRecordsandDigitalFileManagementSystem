<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    :root {
        --red:    #C8102E;
        --blue:   #0B3C9D;
        --yellow: #FFD100;
        --white:  #FAFAFA;
        --dark:   #0a0a1a;
    }

    body {
        font-family: 'Barlow', sans-serif;
        background: var(--dark);
        min-height: 100vh;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    /* Animated gradient background */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 60% at 20% 30%, rgba(11,60,157,0.35) 0%, transparent 70%),
            radial-gradient(ellipse 50% 50% at 80% 70%, rgba(200,16,46,0.25) 0%, transparent 70%),
            radial-gradient(ellipse 40% 40% at 50% 50%, rgba(255,209,0,0.1) 0%, transparent 70%);
        animation: bgPulse 8s ease-in-out infinite alternate;
        pointer-events: none;
    }

    @keyframes bgPulse {
        0%   { opacity: 0.6; }
        100% { opacity: 1; }
    }

    /* Faded logo watermark */
    .bg-logo {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 520px;
        opacity: 0.04;
        pointer-events: none;
        user-select: none;
    }

    /* Decorative corner lines */
    .corner {
        position: fixed;
        width: 120px;
        height: 120px;
        opacity: 0.3;
    }
    .corner-tl { top:20px; left:20px; border-top: 2px solid var(--yellow); border-left: 2px solid var(--yellow); }
    .corner-br { bottom:20px; right:20px; border-bottom: 2px solid var(--red); border-right: 2px solid var(--red); }

    /* HERO */
    .hero {
        position: relative;
        z-index: 10;
        text-align: center;
        padding: 60px 40px;
        animation: heroIn 1s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes heroIn {
        from { opacity:0; transform: translateY(40px); }
        to   { opacity:1; transform: translateY(0); }
    }

    .hero-logo {
        width: 130px;
        filter: drop-shadow(0 0 30px rgba(255,209,0,0.4));
        animation: logoFloat 4s ease-in-out infinite;
        margin-bottom: 28px;
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-10px); }
    }

    .hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.8rem, 6vw, 4.5rem);
        font-weight: 900;
        color: var(--white);
        letter-spacing: -1px;
        line-height: 1;
        margin-bottom: 8px;
    }

    .hero h1 span.sk   { color: var(--yellow); }
    .hero h1 span.docu { color: var(--red); }

    .divider {
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, var(--blue), var(--red));
        margin: 22px auto;
        border-radius: 2px;
    }

    .vismis {
        max-width: 520px;
        margin: 0 auto 40px;
    }

    .vismis-item {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 16px 24px;
        margin-bottom: 12px;
        text-align: left;
        backdrop-filter: blur(8px);
        transition: border-color .3s;
    }

    .vismis-item:hover { border-color: rgba(255,209,0,0.3); }

    .vismis-item .label {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--yellow);
        margin-bottom: 6px;
    }

    .vismis-item p {
        font-size: 0.95rem;
        color: rgba(255,255,255,0.75);
        line-height: 1.5;
        font-weight: 300;
    }

    .login-btn {
        display: inline-block;
        padding: 16px 52px;
        background: linear-gradient(135deg, var(--red), #8b0d1e);
        color: white;
        font-family: 'Barlow', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-decoration: none;
        border-radius: 50px;
        box-shadow: 0 8px 30px rgba(200,16,46,0.4);
        transition: transform .25s, box-shadow .25s, background .25s;
        position: relative;
        overflow: hidden;
    }

    .login-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        border-radius: inherit;
    }

    .login-btn:hover {
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 14px 40px rgba(200,16,46,0.55);
        background: linear-gradient(135deg, #e01535, var(--red));
    }

    .footer-text {
        position: fixed;
        bottom: 18px;
        left: 0; right: 0;
        text-align: center;
        font-size: 0.72rem;
        color: rgba(255,255,255,0.2);
        letter-spacing: 2px;
        text-transform: uppercase;
    }
</style>
</head>
<body>

<img src="sk_logo.png" class="bg-logo" alt="">
<div class="corner corner-tl"></div>
<div class="corner corner-br"></div>

<div class="hero">
    <img src="sk_logo.png" class="hero-logo" alt="SK Logo">

    <h1><span class="sk">SK</span> <span class="docu">Docu</span>Track</h1>
    <p style="color:rgba(255,255,255,0.4); font-size:0.8rem; letter-spacing:3px; text-transform:uppercase; margin-top:4px;">Sangguniang Kabataan Document Management</p>

    <div class="divider"></div>

    <div class="vismis">
        <div class="vismis-item">
            <div class="label">&#128064; Vision</div>
            <p>Empowered youth leading inclusive community development through transparency, integrity, and active civic participation.</p>
        </div>
        <div class="vismis-item">
            <div class="label">&#127919; Mission</div>
            <p>To provide a transparent, organized, and accessible document tracking system that empowers the SK in efficient governance and youth-centered service delivery.</p>
        </div>
    </div>

    <a href="login.php" class="login-btn">&#128274; Login to System</a>
</div>

<p class="footer-text">Sangguniang Kabataan &mdash; Barangay System &copy; <?= date('Y') ?></p>

</body>
</html>