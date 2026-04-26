<?php
/**
 * Endpoint backend para `lista_sr_csv_que`.
 * Consulta la preferencia guardada del usuario y devuelve los defaults del
 * formulario (status, periodo, tipo_activ, ubis compartidas).
 */

use src\actividades\application\ListaSrCsvQueDatos;
use frontend\shared\web\ContestarJson;

$useCase = new ListaSrCsvQueDatos();
$data = $useCase->ejecutar();

ContestarJson::enviar('', $data);
