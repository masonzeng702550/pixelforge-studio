<?php
require_once __DIR__ . '/lib/bootstrap.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Staff SSO is handled by an upstream provider that is currently disabled in
    // this environment, so local sign-in always fails. Public galleries need no login.
    $err = 'Invalid credentials, or SSO is temporarily unavailable.';
}
echo page_header('Sign in');
?>
<section class="card">
  <h1>Sign in to PixelForge</h1>
  <?php if ($err): ?><p class="err"><?= htmlspecialchars($err) ?></p><?php endif; ?>
  <form method="post" autocomplete="off">
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <button type="submit">Sign in</button>
  </form>
  <p class="muted small"><a href="#">Forgot password?</a> &middot; Staff only &mdash; browsing is open to everyone.</p>
</section>
<?php echo page_footer();
