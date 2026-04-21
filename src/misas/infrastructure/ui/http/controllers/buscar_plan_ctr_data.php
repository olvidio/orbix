<?php

use src\misas\application\BuscarPlanCtrData;
use web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

$payload = BuscarPlanCtrData::getData($Qid_zona);
if ($payload['view'] === 'none') {
    ContestarJson::enviar(_('No tiene permiso para ver esta página'));
} else {
    ContestarJson::enviar('', $payload);
}
