<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoGuardar;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\urlsafe_b64decode;

$Qobj_pau = input_string($_POST, 'obj_pau');
$Qid_ubi = input_int($_POST, 'id_ubi');
$Qid_tipo_teleco = input_int($_POST, 'id_tipo_teleco');
$Qdesc_teleco = input_int($_POST, 'id_desc_teleco');
$Qnum_teleco = input_string($_POST, 'num_teleco');
$Qobserv = input_string($_POST, 'observ');
$s_pkey = input_string($_POST, 's_pkey');
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

/** @var TelecoGuardar $useCase */
$useCase = DependencyResolver::get(TelecoGuardar::class);
ContestarJson::enviar('', $useCase->execute(
    $Qobj_pau,
    $Qid_ubi,
    $a_pkey,
    $Qid_tipo_teleco,
    $Qdesc_teleco,
    $Qnum_teleco,
    $Qobserv
));
