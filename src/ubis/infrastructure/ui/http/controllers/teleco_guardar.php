<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoGuardar;
use src\shared\web\ContestarJson;

$Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_pau');
$Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi');
$Qid_tipo_teleco = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_teleco');
$Qdesc_teleco = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_desc_teleco');
$Qnum_teleco = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'num_teleco');
$Qobserv = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'observ');
$s_pkey = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 's_pkey');
$a_sel = $_POST['sel'] ?? [];
if (!is_array($a_sel)) {
    $a_sel = [];
}

/** @var list<int|string> $a_pkey */
$a_pkey = [];
if ($a_sel !== [] && isset($a_sel[0]) && is_string($a_sel[0])) {
    $parts = explode('#', $a_sel[0]);
    $s = str_replace("'", '"', $parts[0]);
    $decoded = json_decode(\src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($s), true);
    if (is_array($decoded)) {
        foreach (array_values($decoded) as $item) {
            if (!is_int($item) && !is_string($item)) {
                continue;
            }
            $a_pkey[] = $item;
        }
    }
} elseif ($s_pkey !== '') {
    $decoded = json_decode(\src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($s_pkey), true);
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
