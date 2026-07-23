<?php
require_once __DIR__ . '/lib/bootstrap.php';
require_once __DIR__ . '/lib/models.php';
require_once __DIR__ . '/lib/gallery.php';
$albums = load_albums();
echo page_header('Make memes in seconds');
?>
<section class="hero">
  <p class="kicker">PixelForge Studio</p>
  <h1>Forge pixels. Ship memes in <span>3 seconds</span>.</h1>
  <p class="lead">The fastest way to remix, caption and share images. No sign-up to browse &mdash;
     just open a gallery and hit <em>share</em>.</p>
  <p>
    <a class="btn" href="/gallery/">Explore galleries</a>
    <a class="btn ghost" href="/about.php">What&rsquo;s new</a>
  </p>
</section>
<section>
  <h2>Trending forges</h2>
  <div class="grid">
    <?php foreach (array_slice($albums, 0, 6) as $a): ?>
      <figure class="tile">
        <a href="<?= htmlspecialchars(make_share_link('date', 1, (string) ($a['slug'] ?? ''))) ?>">
          <img src="<?= svg_thumb((string) ($a['slug'] ?? ''), (string) ($a['title'] ?? '')) ?>" alt="">
          <figcaption><?= htmlspecialchars((string) ($a['title'] ?? '')) ?></figcaption>
        </a>
      </figure>
    <?php endforeach; ?>
  </div>
</section>
<?php echo page_footer();
