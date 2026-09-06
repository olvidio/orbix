<?php

declare(strict_types=1);

namespace src\shared\application\copias;

use PDO;
use RuntimeException;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\shared\infrastructure\persistence\copias\CopiaWriter;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\EsquemaPg;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Throwable;

/**
 * Motor de reconciliación de una tabla copia contra su origen, esquema a esquema.
 *
 * Sirve para dos cosas:
 *   - el reset: dejar la copia igual al origen después de un periodo sin
 *     sincronización incremental;
 *   - el detector de deriva: en modo informe (`$aplicar = false`) un resultado
 *     con cambios significa que la sincronización incremental está fallando.
 *
 * No se borra y recarga: se comparan las filas y sólo se escriben las que
 * difieren, para no generar ruido en la replicación hacia el exterior. Ese
 * detalle es lo que permite ejecutar la reconciliación a menudo: en estado
 * estable no escribe nada.
 *
 * Conexiones: usa el bloque `importar` (usuario de mantenimiento) igual que
 * {@see \src\devel_db_admin\application\MigracionesEjecutar}, porque el cron no
 * tiene sesión y hay que recorrer esquemas ajenos al del login.
 *
 * Cada copia concreta hereda y rellena los huecos: de qué base lee, cómo se
 * construye el contexto de destino y cómo se leen las filas que **deberían**
 * estar en la copia.
 */
abstract class ReconciliadorCopia
{
    /** Tope de filas detalladas por esquema en el informe. */
    protected const MAX_DETALLE = 20;

    public function __construct(
        protected readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        protected readonly CopiaWriter $writer,
    ) {
    }

    // --- Huecos que rellena cada copia --------------------------------------

    /** Clave de esquema del bloque `importar` para la base de origen (p. ej. `publicv`). */
    abstract protected function claveEsquemaOrigen(): string;

    /** Nombre de la base de origen para los mensajes del informe (p. ej. `sv`). */
    abstract protected function nombreBaseOrigen(): string;

    /**
     * Contexto de destino para un esquema de comun, o el **motivo de omisión**
     * si ese esquema no se puede reconciliar (p. ej. no se deduce la dl).
     */
    abstract protected function contextoDe(PDO $pdoComun, string $esquema, int $id_schema): ContextoCopia|string;

    /**
     * Filas que **deberían** estar en la copia de este esquema, ya proyectadas
     * y filtradas por el criterio de negocio de la copia.
     *
     * Debe lanzar excepción si el origen no se puede leer con garantías: un
     * origen que parece vacío por error haría que `--aplicar` borrase la copia
     * entera del esquema.
     *
     * @return array<int, array<string, mixed>> clave => fila
     */
    abstract protected function leerOrigen(PDO $pdoOrigen, string $esquemaOrigen, ContextoCopia $contexto): array;

    /** Esquema de origen correspondiente a un esquema de comun. */
    protected function esquemaOrigenDe(string $esquemaComun): string
    {
        return $esquemaComun . 'v';
    }

    /**
     * Si la copia tiene índices únicos por otra combinación de columnas, las
     * bajas deben aplicarse antes que las altas para no chocar con ellos.
     */
    protected function bajasAntesDeAltas(): bool
    {
        return false;
    }

    // --- Motor ---------------------------------------------------------------

    /**
     * @param bool   $aplicar      false = sólo informe (por defecto)
     * @param string $soloEsquema  esquema comun concreto (p. ej. `H-dlb`); vacío = todos
     * @return array{
     *     aplicado: bool,
     *     esquemas: list<array<string, mixed>>,
     *     totales: array{esquemas: int, altas: int, cambios: int, bajas: int, errores: int},
     *     lineas: list<string>
     * }
     */
    public function execute(bool $aplicar = false, string $soloEsquema = ''): array
    {
        $pdoComun = $this->conectar('public');
        $pdoOrigen = $this->conectar($this->claveEsquemaOrigen());

        $informes = [];
        $lineas = [];
        $totales = ['esquemas' => 0, 'altas' => 0, 'cambios' => 0, 'bajas' => 0, 'errores' => 0];

        foreach ($this->esquemasComun($soloEsquema) as $esquema => $id_schema) {
            $informe = $this->reconciliarEsquema($pdoComun, $pdoOrigen, $esquema, $id_schema, $aplicar);
            $informes[] = $informe->toArray();

            if ($informe->esOmitido()) {
                $lineas[] = sprintf('  %-16s omitido (%s)', $esquema, $informe->omitido);
                continue;
            }

            $totales['esquemas']++;
            if ($informe->tieneError()) {
                $totales['errores']++;
                $lineas[] = sprintf('  %-16s ERROR: %s', $esquema, $informe->error);
                continue;
            }

            $totales['altas'] += $informe->altas;
            $totales['cambios'] += $informe->cambios;
            $totales['bajas'] += $informe->bajas;

            $lineas[] = sprintf(
                '  %-16s origen=%-4d copia=%-4d altas=%-3d cambios=%-3d bajas=%-3d%s',
                $esquema,
                $informe->origen,
                $informe->destino,
                $informe->altas,
                $informe->cambios,
                $informe->bajas,
                $aplicar ? '' : '  (informe)',
            );
        }

        return [
            'aplicado' => $aplicar,
            'esquemas' => $informes,
            'totales' => $totales,
            'lineas' => $lineas,
        ];
    }

    private function reconciliarEsquema(
        PDO $pdoComun,
        PDO $pdoOrigen,
        string $esquema,
        int $id_schema,
        bool $aplicar,
    ): InformeEsquemaCopia {
        $esquemaOrigen = $this->esquemaOrigenDe($esquema);
        if (!EsquemaPg::existeEnPostgres($pdoComun, $esquema)) {
            return InformeEsquemaCopia::omitido($esquema, 'no existe en comun');
        }
        if (!EsquemaPg::existeEnPostgres($pdoOrigen, $esquemaOrigen)) {
            return InformeEsquemaCopia::omitido($esquema, 'no existe en ' . $this->nombreBaseOrigen());
        }

        $contexto = $this->contextoDe($pdoComun, $esquema, $id_schema);
        if (is_string($contexto)) {
            return InformeEsquemaCopia::omitido($esquema, $contexto);
        }

        try {
            $origen = $this->leerOrigen($pdoOrigen, $esquemaOrigen, $contexto);
            $destino = $this->writer->filasActuales($contexto);
            if ($destino === false) {
                return InformeEsquemaCopia::conError(
                    $esquema,
                    'no se ha podido leer ' . $contexto->nombreTabla(),
                );
            }

            $altas = [];
            $cambios = [];
            $detalle = [];
            foreach ($origen as $clave => $fila) {
                if (!isset($destino[$clave])) {
                    $altas[$clave] = $fila;
                    continue;
                }
                $distintas = $this->writer->definicion()->diferencias($fila, $destino[$clave]);
                if ($distintas !== []) {
                    $cambios[$clave] = $fila;
                    if (count($detalle) < static::MAX_DETALLE) {
                        $detalle[] = sprintf('%d: %s', $clave, implode(',', $distintas));
                    }
                }
            }
            $bajas = array_values(array_diff(array_keys($destino), array_keys($origen)));

            // Salvaguarda: si la copia estuviera compartida entre esquemas, no
            // tocar las filas de otra dl aunque no aparezcan en nuestro origen.
            $ajenas = $this->writer->clavesDeOtroEsquema($contexto);
            if ($ajenas !== []) {
                $bajas = array_values(array_diff($bajas, $ajenas));
                $detalle[] = sprintf('%d fila(s) de otro id_schema respetadas', count($ajenas));
            }

            if ($aplicar && ($altas !== [] || $cambios !== [] || $bajas !== [])) {
                $this->aplicarCambios($contexto, $altas + $cambios, $bajas);
            }

            return InformeEsquemaCopia::reconciliado(
                $esquema,
                count($origen),
                count($destino),
                count($altas),
                count($cambios),
                count($bajas),
                $detalle,
            );
        } catch (Throwable $e) {
            return InformeEsquemaCopia::conError($esquema, $e->getMessage());
        }
    }

    /**
     * Todo el esquema en una transacción: o queda reconciliado, o se queda como estaba.
     *
     * @param array<int, array<string, mixed>> $upserts
     * @param list<int> $bajas
     */
    private function aplicarCambios(ContextoCopia $contexto, array $upserts, array $bajas): void
    {
        $pdoComun = $contexto->pdoComun;
        $pdoComun->beginTransaction();
        try {
            if ($this->bajasAntesDeAltas()) {
                $this->eliminar($contexto, $bajas);
                $this->upsertVarios($contexto, $upserts);
            } else {
                $this->upsertVarios($contexto, $upserts);
                $this->eliminar($contexto, $bajas);
            }
            $pdoComun->commit();
        } catch (Throwable $e) {
            if ($pdoComun->inTransaction()) {
                $pdoComun->rollBack();
            }
            throw $e;
        }
    }

    /** @param array<int, array<string, mixed>> $upserts */
    private function upsertVarios(ContextoCopia $contexto, array $upserts): void
    {
        foreach ($upserts as $fila) {
            if ($this->writer->upsert($contexto, $fila) === false) {
                throw new RuntimeException('upsert: ' . $this->writer->getErrorTxt());
            }
        }
    }

    /** @param list<int> $bajas */
    private function eliminar(ContextoCopia $contexto, array $bajas): void
    {
        if ($bajas !== [] && $this->writer->eliminarVarios($contexto, $bajas) === false) {
            throw new RuntimeException('delete: ' . $this->writer->getErrorTxt());
        }
    }

    /**
     * Esquemas de comun, con el mismo criterio que las migraciones multi-esquema.
     *
     * @return array<string, int> esquema => id_schema
     */
    protected function esquemasComun(string $soloEsquema): array
    {
        $esquemas = [];
        foreach ($this->dbSchemaRepository->getDbSchemas(['_ordre' => 'schema']) as $dbSchema) {
            $schema = $dbSchema->getSchema();
            if (EsquemaPg::esEsquemaResto($schema) || EsquemaPg::esEsquemaRegionStgrComun($schema)) {
                continue;
            }
            if ($dbSchema->getId() < 3000 || $dbSchema->getId() >= 4000) {
                continue;
            }
            // Los esquemas `...v` / `...f` son los interiores/exteriores, no los de comun.
            if (str_ends_with($schema, 'v') || str_ends_with($schema, 'f')) {
                continue;
            }
            if ($soloEsquema !== '' && $schema !== $soloEsquema) {
                continue;
            }
            $esquemas[$schema] = $dbSchema->getId();
        }

        if ($soloEsquema !== '' && $esquemas === []) {
            throw new RuntimeException(sprintf('El esquema "%s" no está en el catálogo de comun', $soloEsquema));
        }

        return $esquemas;
    }

    protected function conectar(string $claveEsquema): PDO
    {
        $oConfigDB = new ConfigDB('importar');
        $pdo = (new DBConnection($oConfigDB->getEsquema($claveEsquema)))->getPDO();
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }
}
