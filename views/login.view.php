<?php
/** @var \App\Template $this */
/** @var string|null $success */
/** @var array<string, array<string>> $errors */
/** @var array<string, string> $old */
/** @var \App\Http\Request $request */

$this->extend('layout');
?>

<?php $this->start('title', 'Login') ?>

<section class="login-section">
    <div class="container">
        <h1 class="page-heading">
            Login
        </h1>

        <div class="login-form-wrapper">
            <form method="POST" action="/login" class="login-form">
                <input
                    type="hidden"
                    name="_token"
                    value="<?= htmlspecialchars($request->getCsrfToken()) ?>"
                >
                <div class="form-group">
                    <label for="email">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        aria-required="true"
                    >
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['email'][0]) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                        aria-required="true"
                    >
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['password'][0]) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <button
                    type="submit"
                    class="button"
                >
                    Login
                </button>
            </form>
        </div>
    </div>
</section>
