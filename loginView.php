<?php
$isAdmin = $loginPortal === 'admin';
$portalName = $isAdmin ? 'Superadmin' : 'Employee';
$usernameField = $isAdmin ? 'uname' : 'username';
$submitField = $isAdmin ? 'submit' : 'loginButton';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $portalName; ?> sign in | Oddeven Infotech</title>
    <link rel="stylesheet" href="../dist/css/login.css?v=20261022-2">
</head>
<body class="<?php echo $isAdmin ? 'admin-portal' : 'employee-portal'; ?>">
    <main class="login-shell">
        <section class="brand-panel" aria-label="Oddeven Infotech">
            <a class="brand" href="../"><img src="../images/logo.png" alt="Oddeven Infotech"></a>
            <div class="brand-copy">
                <span class="eyebrow">ODDEVEN WORKSPACE</span>
                <h1><?php echo $isAdmin ? 'A clear view.<br>A better workday.' : 'Your workday,<br>all in one place.'; ?></h1>
                <p><?php echo $isAdmin ? 'Bring your people, projects and business together in one workspace.' : 'Stay connected to your team, keep track of your tasks and make every day count.'; ?></p>
                <div class="feature-list"><span>People</span><span>Projects</span><span>Progress</span></div>
            </div>
            <div class="brand-footer">Oddeven Infotech Pvt. Ltd.<span>Work better, together.</span></div>
        </section>
        <section class="form-panel" aria-labelledby="login-title">
            <div class="form-content">
                <nav class="portal-switch" aria-label="Choose login portal">
                    <a href="../admin/" <?php if ($isAdmin) echo 'aria-current="page"'; ?>>Superadmin</a>
                    <a href="../employee/" <?php if (!$isAdmin) echo 'aria-current="page"'; ?>>Employee</a>
                </nav>
                <span class="eyebrow portal-label"><?php echo $portalName; ?> PORTAL</span>
                <h2 id="login-title">Welcome back</h2>
                <p class="form-intro">Sign in to your <?php echo $isAdmin ? 'admin' : 'employee'; ?> workspace to get started.</p>
                <form method="post">
                    <?php echo oecrm_csrf_field(); ?>
                    <div class="login-feedback" role="alert"><?php echo $loginError; ?></div>
                    <div class="field-group">
                        <label for="login-username">Username</label>
                        <input id="login-username" name="<?php echo $usernameField; ?>" type="text" placeholder="Enter your username" autocomplete="username" autocapitalize="none" spellcheck="false" value="<?php echo htmlspecialchars($_POST[$usernameField] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required autofocus>
                    </div>
                    <div class="field-group">
                        <label for="login-password">Password</label>
                        <div class="password-field">
                            <input id="login-password" name="password" type="password" placeholder="Enter your password" maxlength="13" autocomplete="current-password" required>
                            <button class="password-toggle" type="button" aria-controls="login-password" aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                    </div>
                    <button class="sign-in" type="submit" name="<?php echo $submitField; ?>" value="1">Sign in <span aria-hidden="true">&rarr;</span></button>
                </form>
                <p class="help-text">Need help signing in? Contact your administrator.</p>
            </div>
            <footer class="form-footer">&copy; <?php echo date('Y'); ?> Oddeven Infotech. All rights reserved.</footer>
        </section>
    </main>
    <script>
        const toggle = document.querySelector('.password-toggle');
        const password = document.getElementById('login-password');
        toggle.addEventListener('click', function () {
            const show = password.type === 'password';
            password.type = show ? 'text' : 'password';
            toggle.textContent = show ? 'Hide' : 'Show';
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', String(show));
        });
        document.querySelectorAll('.login-feedback .close').forEach(function (button) {
            button.setAttribute('aria-label', 'Dismiss error');
            button.addEventListener('click', function () { button.closest('.form-group').remove(); });
        });
    </script>
</body>
</html>
