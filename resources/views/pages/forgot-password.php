<div class="box auth">
    <h1>Forgot password</h1>
    <?php if (!empty($sent)): ?>
        <div class="alert ok">If that email belongs to the admin account, a reset link is on its way. It works for 1 hour.</div>
    <?php else: ?>
        <p class="sci" style="margin-top:6px">Enter the account email and we will send you a link to choose a new password.</p>
        <form method="post" action="<?= e(url('/forgot-password')) ?>">
            <div class="form-grid">
                <label>Email
                    <input type="email" name="email" required maxlength="190" autocomplete="email" autofocus>
                </label>
            </div>
            <button class="btn" type="submit">Send reset link</button>
        </form>
    <?php endif; ?>
    <p class="links"><a href="<?= e(url('/login')) ?>">Back to login</a></p>
</div>