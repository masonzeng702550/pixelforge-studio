# PixelForge Studio

Fast, no-friction image remixing and meme sharing. Browse public galleries, tweak
sort/filter state, and hand someone a share link that restores exactly what you saw.

## Run locally

Requires Docker.

```bash
cp .env.example .env      # set your values
docker compose up --build
```

Then open <http://localhost:8088>.

## Configuration

All configuration is environment-driven (see `.env.example`):

| Variable | Default | Purpose |
|----------|---------|---------|
| `CAPTCHA_MODE` | `arith` | Human Shield challenge style (`arith` or `text`) |
| `CAPTCHA_TTL` | `90` | Seconds a Human Shield token stays valid |
| `CAPTCHA_ON_FIRE` | `true` | Token is single-use when true |
| `INJECTION_LEVEL` | `2` | Verbosity of staging banners left in the build |
| `FLAG` | — | Runtime secret token, baked into the image at build time |

## Stack

- PHP 8.2 + Apache (mod_php)
- GD for Human Shield image rendering
- No database — gallery data is served from `storage/albums.json`

## Layout

```
src/
  index.php            landing + trending galleries
  gallery/             gallery browsing + shareable view links
  verify.php           Human Shield verification
  api/debug.php        internal status endpoint
  lib/                 session, models, captcha, gallery helpers
  storage/             album data + render cache
```
