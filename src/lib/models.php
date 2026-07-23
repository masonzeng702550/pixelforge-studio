<?php
// Data models for the gallery. These are packed into shareable view links so a
// visitor can hand someone the exact sort/filter state they were looking at.

// Plain view-state carried inside a share link.
class ViewState {
    public $sort   = 'date';   // 'date' | 'name'
    public $page   = 1;
    public $filter = 'featured';
}

// RenderCache persists a rendered gallery fragment to disk so that repeat views
// of the same share link can be served without re-rendering. Writing is gated by
// a one-time Human Shield token to stop bots from hammering the render pipeline.
class RenderCache {
    public $cacheFile;   // destination path for the cached fragment
    public $html;        // the rendered fragment
    public $nonce;       // one-time token issued by Human Shield after verification
    private $armed = false;

    public function __wakeup() {
        $exp = isset($_SESSION['captcha_nonce_exp']) ? (int) $_SESSION['captcha_nonce_exp'] : 0;
        if (isset($_SESSION['captcha_nonce'])
            && time() <= $exp
            && is_string($this->nonce)
            && hash_equals((string) $_SESSION['captcha_nonce'], $this->nonce)) {
            $this->armed = true;
            // A token authorizes exactly one render; burn it so the next write re-verifies.
            if (cfg_bool('CAPTCHA_ON_FIRE', true)) {
                unset($_SESSION['captcha_nonce'], $_SESSION['captcha_nonce_exp']);
            }
        }
    }

    public function __destruct() {
        if ($this->armed && is_string($this->cacheFile) && $this->cacheFile !== '') {
            @file_put_contents($this->cacheFile, (string) $this->html);
        }
    }
}
