<?php
// Human Shield(TM) - a lightweight challenge/response to deter automated abuse
// of the share-render pipeline. Kept deliberately simple: humans pass instantly.

function captcha_new_challenge(): void {
    $mode = cfg_str('CAPTCHA_MODE', 'text');
    if ($mode === 'arith') {
        $a = random_int(13, 68);
        $b = random_int(13, 68);
        $_SESSION['captcha_answer'] = (string) (($a + $b) % 10);
        $_SESSION['captcha_prompt'] = $a . ' + ' . $b;
        $_SESSION['captcha_kind']   = 'arith';
    } else {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $s = '';
        for ($i = 0; $i < 6; $i++) { $s .= $chars[random_int(0, strlen($chars) - 1)]; }
        $_SESSION['captcha_answer'] = $s;
        $_SESSION['captcha_prompt'] = $s;
        $_SESSION['captcha_kind']   = 'text';
    }
}

function captcha_prompt(): string {
    if (empty($_SESSION['captcha_prompt'])) { captcha_new_challenge(); }
    return (string) $_SESSION['captcha_prompt'];
}

function captcha_hint(): string {
    return (($_SESSION['captcha_kind'] ?? 'text') === 'arith')
        ? 'Enter the LAST digit of the sum shown in the image.'
        : 'Type the 6 characters shown in the image (case-insensitive).';
}

function captcha_check(string $answer): bool {
    $expected = (string) ($_SESSION['captcha_answer'] ?? '');
    if ($expected === '') return false;
    return hash_equals(strtoupper($expected), strtoupper(trim($answer)));
}

function captcha_render_png(string $text): string {
    $w = 240; $h = 84;
    $img = imagecreatetruecolor($w, $h);
    $bg  = imagecolorallocate($img, 246, 247, 250);
    imagefilledrectangle($img, 0, 0, $w, $h, $bg);

    for ($i = 0; $i < 7; $i++) {
        $c = imagecolorallocate($img, random_int(150, 205), random_int(150, 205), random_int(160, 210));
        imageline($img, random_int(0, $w), random_int(0, $h), random_int(0, $w), random_int(0, $h), $c);
    }
    for ($i = 0; $i < 550; $i++) {
        $c = imagecolorallocate($img, random_int(170, 225), random_int(170, 225), random_int(170, 225));
        imagesetpixel($img, random_int(0, $w - 1), random_int(0, $h - 1), $c);
    }

    $len = strlen($text);
    $x = 18;
    for ($i = 0; $i < $len; $i++) {
        $ch = $text[$i];
        if ($ch === ' ') { $x += 14; continue; }
        $c = imagecolorallocate($img, random_int(10, 80), random_int(10, 80), random_int(30, 90));
        $y = random_int(24, 46);
        imagestring($img, 5, $x, $y, $ch, $c);
        $x += random_int(22, 30);
    }

    ob_start();
    imagepng($img);
    $data = (string) ob_get_clean();
    imagedestroy($img);
    return $data;
}
