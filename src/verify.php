<?php
// Human Shield(TM) verification endpoint.
require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/captcha.php';

// Serve the challenge image.
if (isset($_GET['img'])) {
    header('Content-Type: image/png');
    header('Cache-Control: no-store, max-age=0');
    echo captcha_render_png(captcha_prompt());
    exit;
}

$ttl   = cfg_int('CAPTCHA_TTL', 90);
$state = 'idle';   // idle | ok | fail
$nonce = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (captcha_check((string) ($_POST['answer'] ?? ''))) {
        $nonce = bin2hex(random_bytes(6));
        $_SESSION['captcha_nonce']     = $nonce;
        $_SESSION['captcha_nonce_exp'] = time() + $ttl;
        $state = 'ok';
    } else {
        $state = 'fail';
    }
    captcha_new_challenge(); // rotate the challenge for the next attempt
}

// Make sure a challenge exists so the hint text matches the rendered image.
if ($state !== 'ok' && empty($_SESSION['captcha_prompt'])) {
    captcha_new_challenge();
}

echo page_header('Human Shield');
?>
<section class="card verify">
  <h1>Human Shield&trade; verification</h1>
  <p class="muted">To keep bots from abusing PixelForge share links, confirm you&rsquo;re human.</p>

  <?php if ($state === 'ok'): ?>
    <div class="ok">
      <p>&#10004; Verified &mdash; you&rsquo;re cleared for the next <?= (int) $ttl ?> seconds.</p>
      <p>Your one-time share token:</p>
      <p class="nonce"><?= htmlspecialchars($nonce) ?></p>
      <p class="muted small">This token authorizes exactly one share-render, then expires.
         Re-verify to get a fresh one.</p>
      <p><a class="btn ghost small" href="/gallery/">Back to galleries</a></p>
    </div>
  <?php else: ?>
    <?php if ($state === 'fail'): ?>
      <p class="err">That answer didn&rsquo;t match. Try the new image below.</p>
    <?php endif; ?>
    <img class="cap" src="/verify.php?img=1&amp;t=<?= time() ?>" alt="challenge" width="240" height="84">
    <form method="post" autocomplete="off">
      <label><?= htmlspecialchars(captcha_hint()) ?>
        <input type="text" name="answer" inputmode="text" autocomplete="off" required autofocus>
      </label>
      <button type="submit">Verify</button>
    </form>
  <?php endif; ?>
</section>
<?php
echo page_footer();
