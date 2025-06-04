<?php
/** @var \App\Template $this */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */
/** @var \App\Http\Request $request */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $this->section('title') ?> - My Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="navigation-bar">
        <div class="container">
            <div class="header-content">
                <nav class="main-nav">
                    <ul>
                        <li>
                            <a href="/">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="/about">
                                About
                            </a>
                        </li>
                        <li>
                            <a href="/projects">
                                Projects
                            </a>
                        </li>
                        <li>
                            <a href="/contact">
                                Contact
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <nav class="user-nav">
                        <ul>
                            <li>
                                <a href="/admin/dashboard">
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <form
                                    method="POST"
                                    action="/logout"
                                    class="logout-form"
                                >
                                    <input
                                        type="hidden"
                                        name="_token"
                                        value="<?= htmlspecialchars($request->getCsrfToken()) ?>"
                                    >
                                    <button type="submit">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
        </div>
    </header>

    <div class="alerts">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors) && isset($errors['general'])): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors['general'] as $error): ?>
                        <li>
                            <?= htmlspecialchars($error) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <main>
        <?php $this->section('content') ?>
    </main>

    <footer>
        <div class="footer-content">
            <p class="copyright">
                &copy; <?= date('Y') ?>
                My Portfolio. All rights reserved.
            </p>
            <p class="footer-links">
                <a href="/login" class="admin-link">Login</a>
            </p>
        </div>
    </footer>
</body>
</html>
