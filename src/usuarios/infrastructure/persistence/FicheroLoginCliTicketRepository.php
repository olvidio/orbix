<?php

declare(strict_types=1);

namespace src\usuarios\infrastructure\persistence;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\LoginCliTicketRepositoryInterface;

/**
 * Tickets de login para procesos CLI, guardados como ficheros `0600` en `log/login_cli/`.
 *
 * El proceso web y el CLI que lanza corren con el mismo usuario del sistema, así que comparten el
 * directorio. El fichero se nombra con el SHA-256 del ticket (listar el directorio no da tickets
 * válidos) y se consume con un `rename` atómico: dos procesos no pueden usar el mismo ticket.
 */
final class FicheroLoginCliTicketRepository implements LoginCliTicketRepositoryInterface
{
    private const TTL_SEGUNDOS = 120;

    private readonly string $directorio;

    public function __construct(?string $directorio = null, private readonly int $ttl = self::TTL_SEGUNDOS)
    {
        $this->directorio = $directorio ?? ConfigGlobal::$directorio . '/log/login_cli';
    }

    public function emitir(string $username, string $esquema): string
    {
        if (!is_dir($this->directorio) && !mkdir($this->directorio, 0700, true) && !is_dir($this->directorio)) {
            throw new \RuntimeException(sprintf('No se puede crear el directorio de tickets %s', $this->directorio));
        }
        $this->purgarCaducados();

        $ticket = bin2hex(random_bytes(32));
        $datos = json_encode(
            ['u' => $username, 'esq' => $esquema, 'e' => time() + $this->ttl],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
        );
        $ruta = $this->ruta($ticket);
        $umask = umask(0077);
        try {
            if (file_put_contents($ruta, $datos, LOCK_EX) === false) {
                throw new \RuntimeException(sprintf('No se puede escribir el ticket en %s', $this->directorio));
            }
        } finally {
            umask($umask);
        }
        chmod($ruta, 0600);

        return $ticket;
    }

    public function consumir(string $ticket): ?array
    {
        if (preg_match('/^[0-9a-f]{64}$/', $ticket) !== 1) {
            return null;
        }
        $ruta = $this->ruta($ticket);
        $enUso = $ruta . '.' . bin2hex(random_bytes(4));
        if (!@rename($ruta, $enUso)) {
            return null;
        }
        $contenido = file_get_contents($enUso);
        unlink($enUso);
        if ($contenido === false) {
            return null;
        }
        try {
            $datos = json_decode($contenido, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        if (!is_array($datos) || !is_string($datos['u'] ?? null) || !is_string($datos['esq'] ?? null)
            || !is_int($datos['e'] ?? null) || $datos['e'] < time()) {
            return null;
        }

        return ['username' => $datos['u'], 'esquema' => $datos['esq']];
    }

    private function ruta(string $ticket): string
    {
        return $this->directorio . '/' . hash('sha256', $ticket);
    }

    private function purgarCaducados(): void
    {
        $limite = time() - max($this->ttl, self::TTL_SEGUNDOS);
        foreach (glob($this->directorio . '/*') ?: [] as $fichero) {
            $mtime = @filemtime($fichero);
            if ($mtime !== false && $mtime < $limite) {
                @unlink($fichero);
            }
        }
    }
}
