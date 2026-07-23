<?php
// Data models for the gallery. These are packed into shareable view links so a
// visitor can hand someone the exact sort/filter state they were looking at.

// Plain view-state carried inside a share link.
class ViewState {
    public $sort   = 'date';   // 'date' | 'name'
    public $page   = 1;
    public $filter = 'featured';
}

// Low-level cache writer. The target filename is DERIVED from a token, so callers
// never hand it a raw path; the render pipeline uses this to persist fragments.
class ThumbWriter {
    public $baseDir;          // directory to write into
    public $token;            // filename is derived from this
    public $ext = '.html';    // fragment extension
    public $data;             // bytes to persist

    public function write() {
        if (!is_string($this->baseDir) || $this->baseDir === '') return;
        $ext  = is_string($this->ext) ? $this->ext : '.html';
        $name = substr(hash('sha256', (string) $this->token), 0, 16) . $ext;
        @file_put_contents(rtrim((string) $this->baseDir, '/') . '/' . $name, (string) $this->data);
    }
}

// Render cache. On teardown it flushes its pending fragment through the writer,
// but ONLY after a fresh, server-side Human Shield verification for THIS request.
// The check reads the server-issued session token at sink time; there is no
// pre-settable "armed" flag to inject via serialization.
class RenderCache {
    public $writer;   // a ThumbWriter that performs the actual persist
    public $nonce;    // one-time Human Shield token, re-verified below

    public function __destruct() {
        $exp = isset($_SESSION['captcha_nonce_exp']) ? (int) $_SESSION['captcha_nonce_exp'] : 0;
        if (!isset($_SESSION['captcha_nonce']) || time() > $exp) return;
        if (!is_string($this->nonce) || !hash_equals((string) $_SESSION['captcha_nonce'], $this->nonce)) return;

        // A token authorizes exactly one flush; burn it so the next write re-verifies.
        if (cfg_bool('CAPTCHA_ON_FIRE', true)) {
            unset($_SESSION['captcha_nonce'], $_SESSION['captcha_nonce_exp']);
        }
        if (is_object($this->writer) && method_exists($this->writer, 'write')) {
            $this->writer->write();
        }
    }
}
