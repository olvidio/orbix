<?php

declare(strict_types=1);

namespace src\actividadcargos\application;

use PDO;
use PDOException;
use RuntimeException;
use src\actividadcargos\domain\CdCargosActivFila;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivContexto;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivWriter;
use src\devel_db_admin\application\MigracionEjecucionUtiles;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Throwable;

/**
 * Reconcilia la copia `cd_cargos_activ_dl` (BD comun) con `d_cargos_activ_dl`
 * de la BD sv-e, en todos los esquemas o en uno concreto.
 *
 * Sirve para dos cosas:
 *   - el reset: dejar la copia igual al origen después de un periodo sin
 *     sincronización incremental (que es como quedó tras el refactor);
 *   - el detector de deriva: en modo informe (`$aplicar = false`) un resultado
 *     con cambios significa que la sincronización incremental está fallando.
 *
 * No se borra y recarga: se comparan las filas y sólo se escriben las que
 * difieren, para no generar ruido en la replicación hacia el exterior.
 *
 * Conexiones: usa el bloque `importar` (usuario de mantenimiento) igual que
 * {@see \src\devel_db_admin\application\MigracionesEjecutar}, porque el cron no
 * tiene sesión y hay que recorrer esquemas ajenos al del login.
 *
 * Sólo tiene sentido en la instalación **interior sv**: es donde hay acceso
 * de mantenimiento a sv-e y a comun. El driver CLI lo comprueba.
 */
final class ResincronizarCdCargosActiv
{
    private const TABLA_ORIGEN = 'd_cargos_activ_dl';

    /** Tope de cargos detallados por esquema en el informe. */
    private const MAX_DETALLE = 20;

    /** SQLSTATE de «la tabla no existe» / «el esquema no existe». */
    private const SQLSTATE_NO_EXISTE = ['42P01', '3F000'];

    public function __construct(
        private readonly DbSchemaRepositoryInterface $dbSchemaRepository,
        private readonly CdCargosActivWriter $writer,
    ) {
    }

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
        $pdoSve = $this->conectar('publicv-e');

        $esquemas = $this->esquemasComun($soloEsquema);

        $informes = [];
        $lineas = [];
        $totales = ['esquemas' => 0, 'altas' => 0, 'cambios' => 0, 'bajas' => 0, 'errores' => 0];

        foreach ($esquemas as $esquema => $id_schema) {
            $informe = $this->reconciliarEsquema($pdoComun, $pdoSve, $esquema, $id_schema, $aplicar);
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
        PDO $pdoSve,
        string $esquema,
        int $id_schema,
        bool $aplicar,
    ): CdCargosActivInformeEsquema {
        $esquemav = $esquema . 'v';
        if (!MigracionEjecucionUtiles::esquemaExisteEnPostgres($pdoComun, $esquema)) {
            return CdCargosActivInformeEsquema::omitido($esquema, 'no existe en comun');
        }
        if (!MigracionEjecucionUtiles::esquemaExisteEnPostgres($pdoSve, $esquemav)) {
            return CdCargosActivInformeEsquema::omitido($esquema, 'no existe en sv-e');
        }

        $contexto = new CdCargosActivContexto($pdoComun, $esquema, $id_schema);

        try {
            $origen = $this->leerOrigen($pdoSve, $esquemav);
            $destino = $this->writer->filasActuales($contexto);
            if ($destino === false) {
                return CdCargosActivInformeEsquema::conError($esquema, 'no se ha podido leer cd_cargos_activ_dl');
            }

            $altas = [];
            $cambios = [];
            $detalle = [];
            foreach ($origen as $id_item => $fila) {
                if (!isset($destino[$id_item])) {
                    $altas[$id_item] = $fila;
                    continue;
                }
                $distintas = CdCargosActivFila::diferencias($fila, $destino[$id_item]);
                if ($distintas !== []) {
                    $cambios[$id_item] = $fila;
                    if (count($detalle) < self::MAX_DETALLE) {
                        $detalle[] = sprintf('%d: %s', $id_item, implode(',', $distintas));
                    }
                }
            }
            $bajas = array_values(array_diff(array_keys($destino), array_keys($origen)));

            $ajenas = $this->writer->idItemsDeOtroEsquema($contexto);
            if ($ajenas !== []) {
                $bajas = array_values(array_diff($bajas, $ajenas));
                $detalle[] = sprintf('%d fila(s) de otro id_schema respetadas', count($ajenas));
            }

            if ($aplicar && ($altas !== [] || $cambios !== [] || $bajas !== [])) {
                $this->aplicarCambios($contexto, $altas + $cambios, $bajas);
            }

            return CdCargosActivInformeEsquema::reconciliado(
                $esquema,
                count($origen),
                count($destino),
                count($altas),
                count($cambios),
                count($bajas),
                $detalle,
            );
        } catch (Throwable $e) {
            return CdCargosActivInformeEsquema::conError($esquema, $e->getMessage());
        }
    }

    /**
     * Todo el esquema en una transacción: o queda reconciliado, o se queda como estaba.
     *
     * Las bajas van primero: `cd_cargos_activ_dl` suele tener unique
     * `(id_activ, id_cargo)`, y un alta con el mismo par y otro `id_item`
     * chocaría si la baja aún no se ha aplicado.
     *
     * @param array<int, array<string, mixed>> $upserts
     * @param list<int> $bajas
     */
    private function aplicarCambios(CdCargosActivContexto $contexto, array $upserts, array $bajas): void
    {
        $pdoComun = $contexto->pdoComun;
        $pdoComun->beginTransaction();
        try {
            if ($bajas !== [] && $this->writer->eliminarVarios($contexto, $bajas) === false) {
                throw new RuntimeException('delete: ' . $this->writer->getErrorTxt());
            }
            foreach ($upserts as $fila) {
                if ($this->writer->upsert($contexto, $fila) === false) {
                    throw new RuntimeException('upsert: ' . $this->writer->getErrorTxt());
                }
            }
            $pdoComun->commit();
        } catch (Throwable $e) {
            if ($pdoComun->inTransaction()) {
                $pdoComun->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Filas que **deberían** estar en la copia de esta dl.
     *
     * @return array<int, array<string, mixed>> id_item => fila
     */
    private function leerOrigen(PDO $pdoSve, string $esquemav): array
    {
        $filas = [];
        foreach ($this->consultarOrigen($pdoSve, $esquemav) as $registro) {
            $fila = CdCargosActivFila::desdeRegistro($registro);
            if (CdCargosActivFila::debeCopiarse($fila)) {
                $filas[CdCargosActivFila::idItem($fila)] = $fila;
            }
        }

        return $filas;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function consultarOrigen(PDO $pdo, string $esquema): array
    {
        $sql = 'SELECT ' . implode(',', CdCargosActivFila::COLUMNAS)
            . ' FROM ' . self::comillas($esquema) . '.' . self::comillas(self::TABLA_ORIGEN);

        try {
            $stmt = $pdo->query($sql);
            if ($stmt === false) {
                throw new RuntimeException('no se ha podido leer d_cargos_activ_dl');
            }

            $registros = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $registro) {
                if (!is_array($registro)) {
                    continue;
                }
                $normalizado = [];
                foreach ($registro as $clave => $valor) {
                    $normalizado[(string) $clave] = $valor;
                }
                $registros[] = $normalizado;
            }

            return $registros;
        } catch (PDOException $e) {
            // Tabla o esquema inexistentes: omitir el esquema (no tratarlo como
            // origen vacío, que en --aplicar borraría la copia entera).
            if (in_array((string) $e->getCode(), self::SQLSTATE_NO_EXISTE, true)) {
                throw new RuntimeException(
                    sprintf('no existe %s.%s en sv-e', $esquema, self::TABLA_ORIGEN),
                    0,
                    $e,
                );
            }
            throw $e;
        }
    }

    /**
     * Esquemas de comun, con el mismo criterio que las migraciones multi-esquema.
     *
     * @return array<string, int> esquema => id_schema
     */
    private function esquemasComun(string $soloEsquema): array
    {
        $esquemas = [];
        foreach ($this->dbSchemaRepository->getDbSchemas(['_ordre' => 'schema']) as $dbSchema) {
            $schema = $dbSchema->getSchema();
            if (MigracionEjecucionUtiles::esEsquemaResto($schema)) {
                continue;
            }
            if (MigracionEjecucionUtiles::esEsquemaRegionStgrComun($schema)) {
                continue;
            }
            if ($dbSchema->getId() < 3000 || $dbSchema->getId() >= 4000) {
                continue;
            }
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

    private function conectar(string $claveEsquema): PDO
    {
        $oConfigDB = new ConfigDB('importar');
        $pdo = (new DBConnection($oConfigDB->getEsquema($claveEsquema)))->getPDO();
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }

    private static function comillas(string $identificador): string
    {
        return '"' . str_replace('"', '""', $identificador) . '"';
    }
}
