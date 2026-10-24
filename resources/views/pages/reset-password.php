<div class="box auth">
  <h1>Choose a new password</h1>
  <?php if (!empty($invalid)): ?>
    <div class="alert">This reset link is invalid or has expired.</div>
    <p class="links"><a href="<?= e(url('/forgot-password')) ?>">Request a new link</a></p>
  <?php else: ?>
    <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="<?= e(url('/reset-password')) ?>">
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="form-grid">
        <label>New password
          <input type="password" name="password" required minlength="8" autocomplete="new-password" autofocus>
        </label>
        <label>Repeat password
          <input type="password" name="confirm" required minlength="8" autocomplete="new-password">
        </label>
      </div>
      <button class="btn" type="submit">Save password</button>
    </form>
  <?php endif; ?>
</div>