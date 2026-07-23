<?php
// Gallery view. Restores the visitor's sort/filter state from a share link.
require_once __DIR__ . '/../lib/bootstrap.php';
require_once __DIR__ . '/../lib/models.php';
require_once __DIR__ . '/../lib/gallery.php';

$raw   = (string) ($_GET['s'] ?? '');
$state = null;
if ($raw !== '') {
    $decoded = base64_decode($raw, true);
    if ($decoded !== false) {
        // Restore the packed view-state that ships inside the share link.
        $state = @unserialize($decoded);
    }
}
if (!($state instanceof ViewState)) {
    $state = new ViewState();
}

$albums = load_albums();
$album  = $albums[0] ?? ['title' => 'Gallery', 'slug' => '', 'items' => []];
foreach ($albums as $a) {
    if (($a['slug'] ?? '') === (string) $state->filter) { $album = $a; break; }
}
$items = $album['items'] ?? [];
if ((string) $state->sort === 'name') {
    usort($items, fn($x, $y) => strcmp((string) ($x['title'] ?? ''), (string) ($y['title'] ?? '')));
}

echo page_header('Gallery - ' . (string) ($album['title'] ?? ''));
?>
<section>
  <a class="muted" href="/gallery/">&larr; all galleries</a>
  <h1><?= htmlspecialchars((string) ($album['title'] ?? 'Gallery')) ?></h1>
  <p class="muted">sort: <?= htmlspecialchars((string) $state->sort) ?>
     &middot; page <?= (int) $state->page ?>
     &middot; filter: <?= htmlspecialchars((string) $state->filter) ?></p>
  <div class="grid">
    <?php foreach ($items as $it): ?>
      <figure class="tile">
        <img src="<?= svg_thumb((string) ($it['title'] ?? '') . (string) ($album['slug'] ?? ''), (string) ($it['title'] ?? '')) ?>" alt="">
        <figcaption><?= htmlspecialchars((string) ($it['title'] ?? '')) ?></figcaption>
      </figure>
    <?php endforeach; ?>
    <?php if (!$items): ?><p class="muted">This gallery is empty.</p><?php endif; ?>
  </div>
  <p><a class="btn ghost small"
        href="<?= htmlspecialchars(make_share_link((string) $state->sort, (int) $state->page, (string) $state->filter)) ?>">
     Copy share link</a></p>
</section>
<?php
echo page_footer();
