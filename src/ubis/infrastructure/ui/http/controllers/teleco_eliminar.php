<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoEliminar;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\urlsafe_b64decode;

$Qobj_pau = input_string($_POST, 'obj_pau');
$a_sel = $_POST['sel'] ?? [];
if (!is_array($a_sel)) {
    $a_sel = [];
}

/** @var list<int|string> $a_pkey */
$a_pkey = [];
if ($a_sel !== [] && isset($a_sel[0]) && is_string($a_sel[0])) {
    $parts = explode('#', $a_sel[0]);
    $s = str_replace("'", '"', $parts[0]);
    $decoded = json_decode(urlsafe_b64decode($s), true);
    if (is_array($decoded)) {
        foreach (array_values($decoded) as $item) {
            if (is_int($item) || is_string($item)) {
                $a_pkey[] = $item;
            }
        }
    }
}

/** @var TelecoEliminar $useCase */
$useCase = DependencyResolver::get(TelecoEliminar::class);
ContestarJson::enviar('', $useCase->execute($Qobj_pau, $a_pkey));
