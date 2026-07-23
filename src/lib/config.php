<?php
// Runtime paths for PixelForge. The data dir holds boot/cache scratch files and
// is exposed to the app via env. The vault dir is where the deploy pipeline drops
// rotating secrets; it is intentionally NOT an env var so it can be rotated without
// touching the fleet's environment.
define('PIXELFORGE_DATA',  getenv('PIXELFORGE_DATA') ?: '/opt/pixelforge/.runtime');
define('PIXELFORGE_VAULT', '/var/lib/pixelforge/vault');
