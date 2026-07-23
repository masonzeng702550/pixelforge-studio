<?php
require_once __DIR__ . '/../lib/bootstrap.php';
require_once __DIR__ . '/../lib/models.php';
require_once __DIR__ . '/../lib/gallery.php';
$albums = load_albums();
echo page_header('Gallery');
?>
<section>
  <h1>Public galleries</h1>
  <p class="muted">Browse community forges. Every view is shareable &mdash; the link carries your sort &amp; filter state.</p>
  <div class="grid">
    <?php foreach ($albums as $a): ?>
      <figure class="tile">
        <a href="<?= htmlspecialchars(make_share_link('date', 1, (string) ($a['slug'] ?? ''))) ?>">
          <img src="<?= svg_thumb((string) ($a['slug'] ?? ''), (string) ($a['title'] ?? '')) ?>" alt="">
          <figcaption><?= htmlspecialchars((string) ($a['title'] ?? '')) ?>
            <span class="muted">(<?= count($a['items'] ?? []) ?>)</span></figcaption>
        </a>
      </figure>
    <?php endforeach; ?>
  </div>
</section>
<?php echo page_footer();
