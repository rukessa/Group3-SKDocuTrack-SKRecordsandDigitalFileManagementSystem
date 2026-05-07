<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK DocuTrack &mdash; Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Barlow:wght@300;400;600&display=swap" rel="stylesheet">
<style>
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

    :root {
        --red:    #C8102E;
        --blue:   #0B3C9D;
        --yellow: #FFD100;
        --dark:   #0a0a1a;
    }

    body {
        font-family: 'Barlow', sans-serif;
        background: var(--dark);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 70% at 15% 50%, rgba(11,60,157,0.4) 0%, transparent 65%),
            radial-gradient(ellipse 50% 50% at 85% 50%, rgba(200,16,46,0.25) 0%, transparent 65%);
        pointer-events: none;
    }

    .bg-logo {
        position: fixed;
        right: -80px;
        bottom: -80px;
        width: 500px;
        opacity: 0.04;
        pointer-events: none;
    }

    .login-wrap {
        position: relative;
        z-index: 10;
        display: flex;
        gap: 0;
        width: min(880px, 95vw);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0,0,0,0.6);
        animation: cardIn 0.7s cubic-bezier(.22,1,.36,1) both;
    }

    @keyframes cardIn {
        from { opacity:0; transform: translateY(30px) scale(0.97); }
        to   { opacity:1; transform: translateY(0) scale(1); }
    }

    /* LEFT panel */
    .panel-left {
        flex: 1;
        background: linear-gradient(160deg, var(--blue) 0%, #071f5a 100%);
        padding: 60px 44px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .panel-left img {
        width: 90px;
        filter: drop-shadow(0 4px 20px rgba(255,209,0,0.3));
        margin-bottom: 24px;
    }

    .panel-left h2 {
        font-family: 'Playfair Display', serif;
        font-size: 1.9rem;
        color: #fff;
        line-height: 1.2;
        margin-bottom: 12px;
    }

    .panel-left h2 span { color: var(--yellow); }

    .panel-left p {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.5);
        line-height: 1.7;
        font-weight: 300;
    }

    .stripe {
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, var(--yellow), var(--red));
        margin: 20px auto;
        border-radius: 2px;
    }

    /* RIGHT panel - form */
    .panel-right {
        flex: 1.1;
        background: #0f0f20;
        padding: 60px 48px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .panel-right h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 6px;
    }

    .panel-right .sub {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.35);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 36px;
    }

    .field {
        margin-bottom: 20px;
    }

    .field label {
        display: block;
        font-size: 0.7rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.4);
        margin-bottom: 8px;
        font-weight: 600;
    }

    .field input {
        width: 100%;
        padding: 14px 18px;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        color: #fff;
        font-family: 'Barlow', sans-serif;
        font-size: 0.95rem;
        transition: border-color .25s, background .25s;
        outline: none;
    }

    .field input:focus {
        border-color: var(--yellow);
        background: rgba(255,209,0,0.05);
    }

    .field input.error { border-color: var(--red); }

    .field-error {
        font-size: 0.75rem;
        color: #ff6b6b;
        margin-top: 5px;
        display: none;
    }

    .field-error.show { display: block; }

    .login-btn {
        width: 100%;
        padding: 15px;
        margin-top: 8px;
        background: linear-gradient(135deg, var(--red), #8b0d1e);
        color: #fff;
        font-family: 'Barlow', sans-serif;
        font-size: 0.95rem;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        box-shadow: 0 6px 24px rgba(200,16,46,0.35);
        transition: transform .2s, box-shadow .2s;
    }

    .login-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 32px rgba(200,16,46,0.5);
    }

    .login-btn:active { transform: translateY(0); }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        margin-bottom: 20px;
        display: none;
    }

    .alert.error { background: rgba(200,16,46,0.15); border: 1px solid rgba(200,16,46,0.4); color: #ff8080; display: block; }
    .alert.success { background: rgba(0,200,80,0.1); border: 1px solid rgba(0,200,80,0.3); color: #80ffb0; display: block; }

    .back-link {
        margin-top: 28px;
        text-align: center;
        font-size: 0.8rem;
        color: rgba(255,255,255,0.25);
    }

    .back-link a {
        color: var(--yellow);
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        .panel-left { display: none; }
        .panel-right { padding: 40px 28px; }
    }
</style>
</head>
<body>

<img src="sk_logo.png" class="bg-logo" alt="">

<div class="login-wrap">
    <div class="panel-left">
        <img src="sk_logo.png" alt="SK Logo">
        <h2><span>SK</span> DocuTrack</h2>
        <div class="stripe"></div>
        <p>Sangguniang Kabataan<br>Document Management System<br><br>Secure. Organized. Transparent.</p>
    </div>

    <div class="panel-right">
        <h3>Welcome Back</h3>
        <p class="sub">Sign in to your account</p>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert error">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="authenticate.php" method="POST" novalidate>
            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" autocomplete="username">
                <div class="field-error" id="usernameErr">Username is required.</div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password">
                <div class="field-error" id="passwordErr">Password must be at least 6 characters.</div>
            </div>

            <button type="submit" class="login-btn">Login &rarr;</button>
        </form>

        <div class="back-link">
            <a href="index.php">&larr; Back to Home</a>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valid = true;

    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const usernameErr = document.getElementById('usernameErr');
    const passwordErr = document.getElementById('passwordErr');

    // Reset
    username.classList.remove('error');
    password.classList.remove('error');
    usernameErr.classList.remove('show');
    passwordErr.classList.remove('show');

    if (!username.value.trim()) {
        username.classList.add('error');
        usernameErr.classList.add('show');
        valid = false;
    }

    if (password.value.length < 6) {
        password.classList.add('error');
        passwordErr.textContent = password.value.length === 0
            ? 'Password is required.'
            : 'Password must be at least 6 characters.';
        passwordErr.classList.add('show');
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>
</body>
</html>