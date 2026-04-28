<?php
/**
 * Datos para la pantalla plazas_balance_que (opciones dl + id_tipo_activ).
 */

use frontend\shared\web\ContestarJson;
use src\actividadplazas\application\PlazasBalanceQueData;

ContestarJson::enviar('', PlazasBalanceQueData::execute($_POST));
