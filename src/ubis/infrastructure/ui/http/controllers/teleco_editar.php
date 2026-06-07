<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoEditarData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\urlsafe_b64decode;

$Qobj_pau = input_string($_POST, 'obj_pau');
$Qmod = input_string($_POST, 'mod');
$Qid_ubi = input_int($_POST, 'id_ubi');
$a_sel = $_POST['sel'] ?? [];
if (!is_array($a_sel)) {
    $a_sel = [];
}
$s_pkey = input_string($_POST, 's_pkey');

/** @var list<int|string> $a_pkey */
$a_pkey = [];
if ($a_sel !== [] && isset($a_sel[0]) && is_string($a_sel[0])) {
    $parts = explode('#', $a_sel[0]);
    $s = str_replace("'", '"', $parts[0]);
    $decoded = json_decode(urlsafe_b64decode($s), true);
    if (is_array($decoded)) {
        foreach (array_values($decoded) as $item) {
            if (!is_int($item) && !is_string($item)) {
                continue;
            }
            $a_pkey[] = $item;
        }
    }
} elseif ($s_pkey !== '') {
    $decoded = json_decode(urlsafe_b64decode($s_pkey), true);
    if (is_array($decoded)) {
        foreach (array_values($decoded) as $item) {
            if (!is_int($item) && !is_string($item)) {
                continue;
            }
            $a_pkey[] = $item;
        }
    }
}

$pkey = 0;
if (isset($a_pkey[0])) {
    $pkey = is_int($a_pkey[0]) ? $a_pkey[0] : (int) $a_pkey[0];
}

/** @var TelecoEditarData $useCase */
$useCase = DependencyResolver::get(TelecoEditarData::class);
ContestarJson::enviar('', $useCase->execute($Qobj_pau, $Qmod, $Qid_ubi, $pkey));
