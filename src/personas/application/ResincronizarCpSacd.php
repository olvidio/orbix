<?php

declare(strict_types=1);

namespace src\personas\application;

use PDO;
use PDOException;
use RuntimeException;
use src\personas\domain\CpSacdFila;
use src\personas\infrastructure\persistence\postgresql\CpSacdContexto;
use src\personas\infrastructure\persistence\postgresql\CpSacdWriter;
use src\shared\application\copias\ReconciliadorCopia;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Reconcilia la copia `cp_sacd` (BD comun) con las tablas de origen de la BD
 * interior, en todos los esquemas o en uno concreto.
 *
 * El motor (recorrido de esquemas, diff, transacción por esquema, informe) está
 * en {@see ReconciliadorCopia}. Aquí queda lo propio de los sacd: de qué cuatro
 * tablas se lee el origen y con qué filtros.
 *
 * Sólo tiene sentido en la instalación **interior sv**: es donde viven las
 * tablas de origen. Ejecutarlo desde sf vaciaría la copia (sf comparte el
 * esquema de comun pero no tiene las personas). El driver CLI lo comprueba.
 */
final class ResincronizarCpSacd extends ReconciliadorCopia
{
    /** Tablas de origen por id_tabla, en el esquema interior de la dl. */
    private const TABLAS_DL = ['p_numerarios', 'p_agregados', 'p_sssc'];

    /** Las personas de paso viven en un esquema común a todas las dl. */
    private const ESQUEMA_DE_PASO = 'restov';
    private const TABLA_DE_PASO = 'p_de_paso_ex';

    /** Columnas que no existen en `p_de_paso_ex` (se copian como null). */
    private const COLUMNAS_SIN_DE_PASO = ['id_ctr'];

    /** SQLSTATE de «la tabla no existe» / «el esquema no existe». */
    private const SQLSTATE_NO_EXISTE = ['42P01', '3F000'];

    public function __construct(DbSchemaRepositoryInterface $dbSchemaRepository, CpSacdWriter $writer)
    {
        parent::__construct($dbSchemaRepository, $writer);
    }

    protected function claveEsquemaOrigen(): string
    {
        return 'publicv';
    }

    protected function nombreBaseOrigen(): string
    {
        return 'sv';
    }

    protected function contextoDe(PDO $pdoComun, string $esquema, int $id_schema): ContextoCopia|string
    {
        $dl = CpSacdContexto::dlDeEsquema($esquema);
        if ($dl === '') {
            return 'no se puede deducir la dl';
        }

        return new CpSacdContexto($pdoComun, $esquema, $dl, $id_schema);
    }

    /**
     * Personas que **deberían** estar en la copia de esta dl: las sacd de las
     * tres tablas de la dl, más las de paso con `dl = Otra`.
     *
     * @return array<int, array<string, mixed>> id_nom => fila
     */
    protected function leerOrigen(PDO $pdoOrigen, string $esquemaOrigen, ContextoCopia $contexto): array
    {
        $filas = [];

        foreach (self::TABLAS_DL as $tabla) {
            foreach ($this->consultarOrigen($pdoOrigen, $esquemaOrigen, $tabla, false) as $registro) {
                $fila = CpSacdFila::desdeRegistro($registro);
                if (CpSacdFila::debeCopiarse($fila)) {
                    $filas[CpSacdFila::idNom($fila)] = $fila;
                }
            }
        }

        foreach ($this->consultarOrigen($pdoOrigen, self::ESQUEMA_DE_PASO, self::TABLA_DE_PASO, true) as $registro) {
            $fila = CpSacdFila::desdeRegistro($registro);
            if (CpSacdFila::debeCopiarse($fila)) {
                $filas[CpSacdFila::idNom($fila)] = $fila;
            }
        }

        return $filas;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function consultarOrigen(PDO $pdo, string $esquema, string $tabla, bool $dePaso): array
    {
        $columnas = [];
        foreach (CpSacdFila::COLUMNAS as $columna) {
            if ($dePaso && in_array($columna, self::COLUMNAS_SIN_DE_PASO, true)) {
                $columnas[] = 'NULL AS ' . $columna;
                continue;
            }
            $columnas[] = $columna;
        }

        $sql = 'SELECT ' . implode(',', $columnas)
            . ' FROM ' . ContextoCopia::comillas($esquema) . '.' . ContextoCopia::comillas($tabla)
            . ' WHERE sacd IS TRUE';
        $parametros = [];
        if ($dePaso) {
            $sql .= ' AND dl = :dl';
            $parametros['dl'] = CpSacdFila::DL_DE_PASO;
        }

        try {
            $stmt = $pdo->prepare($sql);
            if ($stmt === false) {
                throw new RuntimeException('no se ha podido preparar la consulta de origen');
            }
            $stmt->execute($parametros);

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
            // Tabla o esquema inexistentes (instalaciones antiguas): no es un error.
            // Cualquier otro fallo sí lo es: si lo tragáramos, el origen parecería
            // vacío y en modo --aplicar se borraría la copia entera del esquema.
            if (in_array((string) $e->getCode(), self::SQLSTATE_NO_EXISTE, true)) {
                return [];
            }
            throw $e;
        }
    }
}
