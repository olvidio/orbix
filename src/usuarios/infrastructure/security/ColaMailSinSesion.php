<?php

declare(strict_types=1);

namespace src\usuarios\infrastructure\security;

use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\ColaMailId;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\PgColaMailRepository;

/**
 * Encolar un correo desde un endpoint anónimo (recuperación de contraseña o de 2FA).
 *
 * `PgColaMailRepository` toma la conexión de la global `oDBPC`, que solo existe con sesión:
 * sin ella el constructor lanzaba «PDO global oDBPC no disponible» y los correos de
 * recuperación no se enviaban nunca. Aquí se abre la conexión a `comun.public` y se deja en
 * esa global si no estaba.
 */
final class ColaMailSinSesion
{
    public static function encolar(string $to, string $asunto, string $mensaje, string $escritoPor): bool
    {
        if (!(($GLOBALS['oDBPC'] ?? null) instanceof \PDO)) {
            $config = (new ConfigDB('comun'))->getEsquema('public');
            $GLOBALS['oDBPC'] = (new DBConnection($config))->getPDO();
        }
        $oColaMail = new ColaMail();
        $oColaMail->setUuid_item(ColaMailId::random());
        $oColaMail->setMail_to($to);
        $oColaMail->setSubject($asunto);
        $oColaMail->setMessage($mensaje);
        $oColaMail->setWrited_by($escritoPor);

        return (new PgColaMailRepository())->Guardar($oColaMail);
    }
}
