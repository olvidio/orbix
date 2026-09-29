<?php

declare(strict_types=1);

use src\shared\domain\helpers\FilterPostGet;


/**
 * Verificación de estado del renombre (POST: esquema_origen opcional para solo comprobar el destino; región y dl obligatorios; acepta POST esquema legado con sufijo v/f como origen).
 * Respuesta JSON `data`: objeto con listo, resumen, bloques, meta.
 */

use src\devel_db_admin\application\RenombrarEsquemaVerificacionContexto;
use src\devel_db_admin\application\VerificarEstadoRenombrarEsquema;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;


try {
    HashB::open(
        (string) \src\shared\domain\helpers\FilterPostGet::post('ctx_verificar'),
        'verificar_renombrar_esquema'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$QEsquemaOrigen = trim((string) \src\shared\domain\helpers\FilterPostGet::post('esquema_origen'));
if ($QEsquemaOrigen === '') {
    $QEsquemaOrigen = RenombrarEsquemaVerificacionContexto::baseDesdeCampoOrigen((string) \src\shared\domain\helpers\FilterPostGet::post('esquema'));
}
$Qregion = (string) \src\shared\domain\helpers\FilterPostGet::post('region');
$Qdl = (string) \src\shared\domain\helpers\FilterPostGet::post('dl');
$Qcomun = (int) \src\shared\domain\helpers\FilterPostGet::post('comun');
$Qsv = (int) \src\shared\domain\helpers\FilterPostGet::post('sv');
$Qsf = (int) \src\shared\domain\helpers\FilterPostGet::post('sf');

$payload = (new VerificarEstadoRenombrarEsquema())->ejecutar(
    $QEsquemaOrigen,
    $Qregion,
    $Qdl,
    $Qcomun,
    $Qsv,
    $Qsf,
);

ContestarJson::enviar('', $payload);
