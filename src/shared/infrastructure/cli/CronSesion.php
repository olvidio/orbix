<?php

declare(strict_types=1);

namespace src\shared\infrastructure\cli;

use RuntimeException;

/**
 * Carga el fichero de sesión para crons CLI (fuera del repo) y lo aplica
 * a `$_POST` / `$_SERVER` / `putenv`, el mismo contrato que los ocho
 * argumentos posicionales de `avisos_generar_tabla.php`.
 *
 * El fichero es un PHP que hace `return [ 'username' => ..., ... ]`.
 */
final class CronSesion
{
    private const CLAVES = [
        'username',
        'password',
        'dirweb',
        'document_root',
        'ubicacion',
        'esquema',
        'private',
        'db_server',
    ];

    /**
     * @param array<string, string> $datos
     */
    public function __construct(private readonly array $datos)
    {
        foreach (self::CLAVES as $clave) {
            if (!isset($this->datos[$clave]) || !is_string($this->datos[$clave]) || $this->datos[$clave] === '') {
                throw new RuntimeException(sprintf('cron_sesion.inc: falta la clave «%s»', $clave));
            }
        }
    }

    public static function desdeFichero(string $ruta): self
    {
        if (!is_readable($ruta)) {
            throw new RuntimeException(sprintf('No se puede leer el fichero de sesión: %s', $ruta));
        }
        $datos = require $ruta;
        if (!is_array($datos)) {
            throw new RuntimeException(sprintf('cron_sesion.inc debe devolver un array: %s', $ruta));
        }

        /** @var array<string, mixed> $datos */
        $planos = [];
        foreach ($datos as $clave => $valor) {
            if (!is_string($clave)) {
                continue;
            }
            $planos[$clave] = is_scalar($valor) ? (string) $valor : '';
        }

        return new self($planos);
    }

    /**
     * @param list<string> $candidatos
     */
    public static function buscar(array $candidatos): string
    {
        foreach ($candidatos as $ruta) {
            if ($ruta !== '' && is_readable($ruta)) {
                return $ruta;
            }
        }

        throw new RuntimeException(
            'No se encontró cron_sesion.inc. Pasa --sesion=/ruta o ORBIX_CRON_SESION.',
        );
    }

    public function aplicar(): void
    {
        $_POST['username'] = $this->datos['username'];
        $_POST['password'] = $this->datos['password'];
        $_SERVER['DIRWEB'] = $this->datos['dirweb'];
        $_SERVER['DOCUMENT_ROOT'] = $this->datos['document_root'];
        putenv('UBICACION=' . $this->datos['ubicacion']);
        putenv('ESQUEMA=' . $this->datos['esquema']);
        putenv('PRIVATE=' . $this->datos['private']);
        putenv('DB_SERVER=' . $this->datos['db_server']);
    }

    public function ubicacion(): string
    {
        return strtolower(trim($this->datos['ubicacion']));
    }

    /** Destino de avisos si falla la reconciliación. Vacío: no se encola mail. */
    public function mailAviso(): string
    {
        return trim($this->datos['mail_aviso'] ?? '');
    }
}
