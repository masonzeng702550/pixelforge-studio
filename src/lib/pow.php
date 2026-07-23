<?php
// Human Shield(TM) proof-of-work gate.
//
// In the spirit of the kctf / redpwn / SekaiCTF PoW that gates challenge
// front-ends against automated abuse. A client must find a nonce whose
// sha256(prefix || nonce) has POW_BITS leading zero bits. Verifying costs one
// hash; solving costs ~2^POW_BITS hashes, so every share-render attempt carries
// a real, tunable compute cost that cannot be OCR'd or shortcut.
//
// Challenge wire format:  PF1;<bits>;<prefix-hex>
// Solution:               the nonce string

function pow_bits(): int {
    $b = cfg_int('POW_BITS', 22);
    if ($b < 8)  $b = 8;
    if ($b > 28) $b = 28;
    return $b;
}

function pow_new_challenge(): void {
    $_SESSION['pow_prefix'] = bin2hex(random_bytes(8));
    $_SESSION['pow_k']      = pow_bits();
}

function pow_token(): string {
    if (empty($_SESSION['pow_prefix'])) { pow_new_challenge(); }
    return 'PF1;' . (int) $_SESSION['pow_k'] . ';' . $_SESSION['pow_prefix'];
}

function pow_leading_zero_bits(string $raw): int {
    $n = 0;
    $len = strlen($raw);
    for ($i = 0; $i < $len; $i++) {
        $b = ord($raw[$i]);
        if ($b === 0) { $n += 8; continue; }
        for ($bit = 7; $bit >= 0; $bit--) {
            if (($b >> $bit) & 1) return $n;
            $n++;
        }
        return $n;
    }
    return $n;
}

function pow_check(string $nonce): bool {
    $prefix = (string) ($_SESSION['pow_prefix'] ?? '');
    $k      = (int) ($_SESSION['pow_k'] ?? 0);
    if ($prefix === '' || $k <= 0) return false;
    if ($nonce === '' || strlen($nonce) > 64) return false;
    $raw = hash('sha256', $prefix . $nonce, true);
    return pow_leading_zero_bits($raw) >= $k;
}
