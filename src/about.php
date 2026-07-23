<?php
require_once __DIR__ . '/lib/bootstrap.php';
echo page_header('About');
?>
<section class="prose">
  <h1>About PixelForge Studio</h1>
  <p>PixelForge started in a dorm room in 2024 with a simple goal: make image remixing
     so fast it feels like typing. Today makers use it to caption, crop and share thousands
     of forges a day.</p>

  <h2>Changelog</h2>
  <ul>
    <li><strong>v2.6</strong> &mdash; Introducing <em>Human Shield&trade;</em>: a lightweight
        check that keeps automated scrapers from abusing our share-render pipeline.
        Bots get bounced; humans breeze through in a second.</li>
    <li><strong>v2.5</strong> &mdash; Faster share links &mdash; your view-state (sort, page,
        filter) is now packed straight into the URL, so a link restores exactly what you saw.</li>
    <li><strong>v2.4</strong> &mdash; New gradient thumbnails and a lighter theme.</li>
    <li><strong>v2.3</strong> &mdash; Public galleries no longer require an account to browse.</li>
  </ul>

  <p class="muted">Press &amp; partnerships: hello@pixelforge.example</p>
</section>
<?php echo page_footer();
