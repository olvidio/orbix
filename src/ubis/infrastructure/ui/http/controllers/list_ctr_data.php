<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\ListCtrData;
use src\shared\web\ContestarJson;

$Qque_lista = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'que_lista');
$Qloc = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'loc');
if ($Qloc === '') {
    ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'loc'), 'none');
    return;
}
$Qid_sel = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_sel');
$Qscroll_id = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'scroll_id');

ContestarJson::enviar('', DependencyResolver::get(ListCtrData::class)->execute($Qloc, $Qque_lista, $Qid_sel, $Qscroll_id));
