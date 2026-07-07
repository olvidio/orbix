<?php

declare(strict_types=1);

use frontend\shared\FrontBootstrap;
use frontend\shared\web\NavEphemeralFields;

require_once __DIR__ . '/../FrontBootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$oPosicion = FrontBootstrap::boot();

$patchJson = filter_input(INPUT_POST, 'nav_patch', FILTER_UNSAFE_RAW);
$patch = [];
if (is_string($patchJson) && $patchJson !== '') {
    $decoded = json_decode($patchJson, true);
    if (is_array($decoded)) {
        /** @var array<string, mixed> $normalized */
        $normalized = [];
        foreach ($decoded as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
            }
        }
        $patch = NavEphemeralFields::strip($normalized);
    }
}

$oPosicion->nav()->updateState($patch);

echo json_encode(['ok' => true], JSON_THROW_ON_ERROR);
