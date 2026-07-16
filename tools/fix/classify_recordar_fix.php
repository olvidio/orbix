#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$json = json_decode(shell_exec('php ' . escapeshellarg($root . '/tools/audit/audit_posicion_nav_migration.php') . ' --only=recordar_sin_post_limpio --json 2>/dev/null') ?: '{}', true);
$files = $json['recordar_sin_post_limpio'] ?? [];
$counts = ['POST' => 0, 'GOBACK' => 0, 'RESTORE' => 0];

foreach ($files as $rel) {
    $c = file_get_contents($root . '/' . $rel);
    if (!preg_match('/->recordar\s*\(/', $c, $m, PREG_OFFSET_CAPTURE)) {
        continue;
    }
    $before = substr($c, 0, $m[0][1]);
    $restore = str_contains($before, 'goStack');
    $aGoBack = (bool) preg_match('/\$aGoBack\s*=\s*\[/', $c);
    if (!$restore) {
        $cat = 'POST';
    } elseif ($aGoBack) {
        $cat = 'GOBACK';
    } else {
        $cat = 'RESTORE';
    }
    $counts[$cat]++;
    echo "$cat:$rel\n";
}
fwrite(STDERR, json_encode($counts) . "\n");
