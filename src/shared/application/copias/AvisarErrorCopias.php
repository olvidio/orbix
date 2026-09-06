<?php

declare(strict_types=1);

namespace src\shared\application\copias;

use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\entity\ColaMail;
use src\shared\domain\value_objects\ColaMailId;

/**
 * Encola un aviso en `cola_mails` si la reconciliación de copias ha fallado.
 * El cron de la DMZ (`enviar_mails_en_cola.php`) es quien lo manda; no se usa
 * el mail() del servidor interior.
 */
final class AvisarErrorCopias
{
    public const WRITED_BY = 'copias_resincronizar';

    public function __construct(private ColaMailRepositoryInterface $colaMailRepository)
    {
    }

    /**
     * @param array{
     *     tareas?: list<array{nombre: string, omitida: ?string, resultado: ?array, error: ?string}>,
     *     errores?: int
     * }|null $resultado
     */
    public function execute(string $mailTo, string $ubicacion, ?array $resultado, ?string $excepcion = null): bool
    {
        $mailTo = trim($mailTo);
        if ($mailTo === '' || filter_var($mailTo, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }
        if (($resultado['errores'] ?? 0) <= 0 && ($excepcion === null || $excepcion === '')) {
            return false;
        }

        $mail = new ColaMail();
        $mail->setUuid_item(ColaMailId::random());
        $mail->setMail_to($mailTo);
        $mail->setSubject(sprintf('[Orbix] Error al resincronizar copias (%s)', $ubicacion ?: '?'));
        $mail->setMessage($this->cuerpo($ubicacion, $resultado, $excepcion));
        $mail->setHeaders(
            "MIME-Version: 1.0\r\n"
            . "Content-type: text/plain; charset=utf-8\r\n"
            . "From: Aquinate <no-Reply@moneders.net>\r\n"
            . "Reply-To: no-Reply@moneders.net\r\n"
            . "Return-path: no-Reply@moneders.net\r\n"
        );
        $mail->setWrited_by(self::WRITED_BY);

        return $this->colaMailRepository->Guardar($mail);
    }

    /**
     * @param array{
     *     tareas?: list<array{nombre: string, omitida: ?string, resultado: ?array, error: ?string}>,
     *     errores?: int
     * }|null $resultado
     */
    public function cuerpo(string $ubicacion, ?array $resultado, ?string $excepcion): string
    {
        $lineas = [
            sprintf('Resincronización de copias en %s: ha habido errores.', $ubicacion ?: '?'),
            '',
        ];
        if ($excepcion !== null && $excepcion !== '') {
            $lineas[] = 'Excepción: ' . $excepcion;
            $lineas[] = '';
        }
        foreach ($resultado['tareas'] ?? [] as $tarea) {
            if (($tarea['omitida'] ?? null) !== null) {
                $lineas[] = sprintf('%s  omitida: %s', $tarea['nombre'], $tarea['omitida']);
                continue;
            }
            if (($tarea['error'] ?? null) !== null) {
                $lineas[] = sprintf('%s  ERROR: %s', $tarea['nombre'], $tarea['error']);
                continue;
            }
            $totales = $tarea['resultado']['totales'] ?? [];
            $lineas[] = sprintf(
                '%s  esquemas=%d altas=%d cambios=%d bajas=%d errores=%d',
                $tarea['nombre'],
                $totales['esquemas'] ?? 0,
                $totales['altas'] ?? 0,
                $totales['cambios'] ?? 0,
                $totales['bajas'] ?? 0,
                $totales['errores'] ?? 0,
            );
            foreach ($tarea['resultado']['lineas'] ?? [] as $linea) {
                if (is_string($linea) && str_contains($linea, 'ERROR')) {
                    $lineas[] = $linea;
                }
            }
        }

        return implode("\n", $lineas) . "\n";
    }
}
