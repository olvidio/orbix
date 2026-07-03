<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\ListCtrData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qque_lista = FuncTablasSupport::inputString($_POST, 'que_lista');
$Qloc = FuncTablasSupport::inputString($_POST, 'loc');
$Qid_sel = FuncTablasSupport::inputString($_POST, 'id_sel');
$Qscroll_id = FuncTablasSupport::inputString($_POST, 'scroll_id');

ContestarJson::enviar('', DependencyResolver::get(ListCtrData::class)->execute($Qloc, $Qque_lista, $Qid_sel, $Qscroll_id));
