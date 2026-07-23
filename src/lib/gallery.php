<?php
// Gallery data access + presentation helpers.

function load_albums(): array {
    $f = __DIR__ . '/../storage/albums.json';
    $j = is_file($f) ? json_decode((string) file_get_contents($f), true) : [];
    return is_array($j) ? $j : [];
}

// Deterministic gradient placeholder so we ship zero binary image assets.
function svg_thumb(string $seed, string $label): string {
    $h  = crc32($seed);
    $c1 = sprintf('#%02x%02x%02x', 110 + ($h & 0x6f), 70 + (($h >> 7) & 0x7f), 150 + (($h >> 14) & 0x4f));
    $c2 = sprintf('#%02x%02x%02x', 50 + (($h >> 3) & 0x6f), 110 + (($h >> 9) & 0x7f), 90 + (($h >> 16) & 0x6f));
    $lbl = htmlspecialchars($label, ENT_QUOTES);
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="200">'
      . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
      . '<stop offset="0" stop-color="' . $c1 . '"/><stop offset="1" stop-color="' . $c2 . '"/>'
      . '</linearGradient></defs><rect width="300" height="200" fill="url(#g)"/>'
      . '<text x="16" y="182" font-family="sans-serif" font-size="15" fill="rgba(255,255,255,.9)">' . $lbl . '</text>'
      . '</svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

// Build a share link that packs the current view-state into the URL.
function make_share_link(string $sort, int $page, string $filter): string {
    $vs = new ViewState();
    $vs->sort   = $sort;
    $vs->page   = $page;
    $vs->filter = $filter;
    return '/gallery/view.php?s=' . urlencode(base64_encode(serialize($vs)));
}
