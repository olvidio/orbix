<?php

declare(strict_types=1);

namespace src\cambios\infrastructure\cli;

use src\cambios\domain\contracts\AvisosGenerarTablaLanzadorInterface;
use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\LoginCliTicketRepositoryInterface;

/**
 * Lanza `avisos_generar_tabla.php` con `nohup` como el usuario de la sesión.
 *
 * El proceso inicia sesión con un ticket de un solo uso que va en la variable de entorno
 * `ORBIX_LOGIN_CLI_TICKET` (no en la línea de órdenes, que se ve con `ps`); la posición de la
 * contraseña lleva «-». Todos los argumentos van con `escapeshellarg`.
 *
 * Argumentos posicionales del script: usuario, contraseña, dirweb, document_root, ubicación,
 * esquema, private y DB_SERVER (ver el README de `cambios`).
 */
final class AvisosGenerarTablaLanzadorExec implements AvisosGenerarTablaLanzadorInterface
{
    private const ESPERA_SEGUNDOS = 60;

    public function __construct(private readonly LoginCliTicketRepositoryInterface $ticketsCli)
    {
    }

    public function lanzarYEsperar(): void
    {
        exec($this->comando());
        sleep(self::ESPERA_SEGUNDOS);
    }

    public function comando(): string
    {
        $program = ConfigGlobal::$directorio . '/src/cambios/infrastructure/cli/avisos_generar_tabla.php';
        $err = ConfigGlobal::$directorio . '/log/avisos.err';
        $out = ConfigGlobal::$directorio . '/log/avisos.out';

        $username = ConfigGlobal::mi_usuario();
        $esquemaSesion = ConfigGlobal::mi_region_dl();
        $dirweb = isset($_SERVER['DIRWEB']) && is_string($_SERVER['DIRWEB']) ? $_SERVER['DIRWEB'] : '';
        $doc_root = isset($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';
        $ubicacion = (string) getenv('UBICACION');
        $esquema_web = (string) getenv('ESQUEMA');
        $private = (string) getenv('PRIVATE');
        $private = $private === '' ? 'x' : $private;
        $db_server = (string) getenv('DB_SERVER');
        // Si se entró eligiendo el esquema en el desplegable, la variable de entorno está vacía.
        if ($esquema_web === '') {
            $esquema_web = $esquemaSesion;
        }

        $ticket = $this->ticketsCli->emitir($username, $esquemaSesion);
        $args = [$program, $username, '-', $dirweb, $doc_root, $ubicacion, $esquema_web, $private, $db_server];

        return 'ORBIX_LOGIN_CLI_TICKET=' . escapeshellarg($ticket)
            . ' nohup /usr/bin/php ' . implode(' ', array_map('escapeshellarg', $args))
            . ' >> ' . escapeshellarg($out) . ' 2>> ' . escapeshellarg($err) . ' < /dev/null &';
    }
}
