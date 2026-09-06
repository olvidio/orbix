<?php

declare(strict_types=1);

namespace src\actividadcargos\application;

use PDO;
use PDOException;
use RuntimeException;
use src\actividadcargos\domain\CdCargosActivFila;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivContexto;
use src\actividadcargos\infrastructure\persistence\postgresql\CdCargosActivWriter;
use src\shared\application\copias\ReconciliadorCopia;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

/**
 * Reconcilia la copia `cd_cargos_activ_dl` (BD comun) con `d_cargos_activ_dl`
 * de la BD sv-e, en todos los esquemas o en uno concreto.
 *
 * El motor (recorrido de esquemas, diff, transacción por esquema, informe) está
 * en {@see ReconciliadorCopia}. Aquí queda lo propio de los cargos: la tabla de
 * origen y el orden de aplicación.
 *
 * Sólo tiene sentido en la instalación **interior sv**: es donde hay acceso
 * de mantenimiento a sv-e y a comun. El driver CLI lo comprueba.
 */
final class ResincronizarCdCargosActiv extends ReconciliadorCopia
{
    private const TABLA_ORIGEN = 'd_cargos_activ_dl';

    /** SQLSTATE de «la tabla no existe» / «el esquema no existe». */
    private const SQLSTATE_NO_EXISTE = ['42P01', '3F000'];

    public function __construct(DbSchemaRepositoryInterface $dbSchemaRepository, CdCargosActivWriter $writer)
    {
        parent::__construct($dbSchemaRepository, $writer);
    }

    protected function claveEsquemaOrigen(): string
    {
        return 'publicv-e';
    }

    protected function nombreBaseOrigen(): string
    {
        return 'sv-e';
    }

    /** Esta copia nunca omite un esquema por el contexto: no depende de la dl. */
    protected function contextoDe(PDO $pdoComun, string $esquema, int $id_schema): ContextoCopia
    {
        return new CdCargosActivContexto($pdoComun, $esquema, $id_schema);
    }

    /**
     * `cd_cargos_activ_dl` suele tener unique `(id_activ, id_cargo)`, y un alta
     * con el mismo par y otro `id_item` chocaría si la baja aún no se ha aplicado.
     */
    protected function bajasAntesDeAltas(): bool
    {
        return true;
    }

    /**
     * Cargos que **deberían** estar en la copia de este esquema.
     *
     * @return array<int, array<string, mixed>> id_item => fila
     */
    protected function leerOrigen(PDO $pdoOrigen, string $esquemaOrigen, ContextoCopia $contexto): array
    {
        $filas = [];
        foreach ($this->consultarOrigen($pdoOrigen, $esquemaOrigen) as $registro) {
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
            . ' FROM ' . ContextoCopia::comillas($esquema) . '.' . ContextoCopia::comillas(self::TABLA_ORIGEN);

        try {
            $stmt = $pdo->query($sql);
            if ($stmt === false) {
                throw new RuntimeException('no se ha podido leer ' . self::TABLA_ORIGEN);
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
            // Tabla o esquema inexistentes: marcar el esquema como error (no
            // tratarlo como origen vacío, que en --aplicar borraría la copia entera).
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
}
