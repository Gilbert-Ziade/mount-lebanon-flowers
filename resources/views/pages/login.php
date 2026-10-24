<div class="box auth">
  <h1>Admin login</h1>
  <?php if (!empty($notice)): ?><div class="alert ok"><?= e($notice) ?></div><?php endif; ?>
  <?php if (!empty($error)): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="<?= e(url('/login')) ?>">
    <div class="form-grid">
      <label>Username
        <input name="username" required maxlength="60" autocomplete="username" autofocus value="<?= e($username ?? '') ?>">
      </label>
      <label>Password
        <input type="password" name="password" required autocomplete="current-password">
      </label>
    </div>
    <button class="btn" type="submit">Log in</button>
  </form>
  <p class="links"><a href="<?= e(url('/forgot-password')) ?>">Forgot your password?</a></p>
</div>