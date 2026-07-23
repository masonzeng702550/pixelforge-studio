<?php
// Human Shield(TM) verification endpoint - proof-of-work gate.
require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/pow.php';

$ttl   = cfg_int('CAPTCHA_TTL', 90);
$state = 'idle';   // idle | ok | fail
$nonce = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (pow_check((string) ($_POST['solution'] ?? ''))) {
        $nonce = bin2hex(random_bytes(6));
        $_SESSION['captcha_nonce']     = $nonce;
        $_SESSION['captcha_nonce_exp'] = time() + $ttl;
        $state = 'ok';
    } else {
        $state = 'fail';
    }
    pow_new_challenge(); // rotate the work factor for the next attempt
}

$token = pow_token();
$k     = (int) $_SESSION['pow_k'];

echo page_header('Human Shield');
?>
<section class="card verify">
  <h1>Human Shield&trade; verification</h1>
  <p class="muted">To keep bots from hammering PixelForge share links, every share-render
     is gated by a small proof-of-work. Solve it once to get a one-time token.</p>

  <?php if ($state === 'ok'): ?>
    <div class="ok">
      <p>&#10004; Verified &mdash; cleared for the next <?= (int) $ttl ?> seconds.</p>
      <p>Your one-time share token:</p>
      <p class="nonce"><?= htmlspecialchars($nonce) ?></p>
      <p class="muted small">Authorizes exactly one share-render, then expires. Re-solve for a fresh one.</p>
      <p><a class="btn ghost small" href="/gallery/">Back to galleries</a></p>
    </div>
  <?php else: ?>
    <?php if ($state === 'fail'): ?>
      <p class="err">That solution didn&rsquo;t satisfy the work factor. Solve the new challenge below.</p>
    <?php endif; ?>
    <p>Challenge (find a <code>nonce</code> so that
       <code>sha256(prefix + nonce)</code> starts with <strong><?= $k ?></strong> zero bits):</p>
    <p class="nonce"><?= htmlspecialchars($token) ?></p>
    <p class="muted small">Solve with our helper &mdash;
       <a href="/pow_solve.py">pow_solve.py</a>:</p>
    <pre class="code">python3 pow_solve.py '<?= htmlspecialchars($token) ?>'</pre>
    <form method="post" autocomplete="off">
      <label>Paste the nonce it prints
        <input type="text" name="solution" inputmode="text" autocomplete="off" required autofocus>
      </label>
      <button type="submit">Verify</button>
    </form>
  <?php endif; ?>
</section>
<?php
echo page_footer();
