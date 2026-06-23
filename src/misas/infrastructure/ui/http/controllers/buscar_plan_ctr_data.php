<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\BuscarPlanCtrData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_post('id_zona', FILTER_VALIDATE_INT);

/** @var BuscarPlanCtrData $useCase */
$useCase = DependencyResolver::get(BuscarPlanCtrData::class);
$result = $useCase->getData($Qid_zona);
if ($result['view'] === 'none') {
    ContestarJson::enviar(_('No tiene permiso para ver esta página'));
} else {
    ContestarJson::enviar('', $result);
}
