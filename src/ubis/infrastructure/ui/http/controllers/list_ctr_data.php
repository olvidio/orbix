<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\ListCtrData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$Qque_lista = input_string($_POST, 'que_lista');
$Qloc = input_string($_POST, 'loc');
$Qid_sel = input_string($_POST, 'id_sel');
$Qscroll_id = input_string($_POST, 'scroll_id');

ContestarJson::enviar('', DependencyResolver::get(ListCtrData::class)->execute($Qloc, $Qque_lista, $Qid_sel, $Qscroll_id));
