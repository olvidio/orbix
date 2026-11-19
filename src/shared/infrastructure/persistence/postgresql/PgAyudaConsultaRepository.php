<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\postgresql;

use PDO;
use Ramsey\Uuid\Uuid as RamseyUuid;
use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\criteria\AyudaConsultaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\domain\value_objects\AyudaConsultaEstado;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Adapta la tabla `ayuda_consultas` (BD comun, esquema public) a la interfaz del repositorio.
 * Lee y escribe por la misma conexión: lo recién registrado tiene que poder valorarse enseguida.
 */
class PgAyudaConsultaRepository extends ClaseRepository implements AyudaConsultaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('ayuda_consultas');
    }

    /**
     * @return list<AyudaConsulta>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(AyudaConsultaField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = AyudaConsulta::fromArray($this->hidratar($aDatos));
        }

        return $result;
    }

    public function findById(string $uuid_item): ?AyudaConsulta
    {
        // La columna es uuid: un texto que no lo es no puede existir (y la consulta fallaría).
        if (!RamseyUuid::isValid($uuid_item)) {
            return null;
        }
        $sql = 'SELECT * FROM ' . $this->getNomTabla() . ' WHERE uuid_item = :w_uuid_item';
        $stmt = $this->prepareAndExecute($this->getoDbl(), $sql, [':w_uuid_item' => $uuid_item], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return null;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return null;
        }

        return AyudaConsulta::fromArray($this->hidratar($aDatos));
    }

    /**
     * Si no existe el registro hace un insert; si existe, el update.
     */
    public function Guardar(AyudaConsulta $ayudaConsulta): bool
    {
        $uuid_item = $ayudaConsulta->getUuid_item()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $aDatos = $ayudaConsulta->toArrayForDatabase([
            'uuid_item' => static fn ($v): string => (string) $v,
            'f_consulta' => static fn ($v) => (new ConverterDate('timestamp', $v))->toPg(),
            // «0 resultados» es un dato, no un vacío que haya que guardar como NULL.
            'n_resultados' => static fn ($v): string => (string) (int) $v,
        ]);

        if ($this->findById($uuid_item) !== null) {
            unset($aDatos['uuid_item']);
            $sql = "UPDATE $nom_tabla SET
                    f_consulta   = :f_consulta,
                    pregunta     = :pregunta,
                    esquema      = :esquema,
                    pantalla     = :pantalla,
                    n_resultados = :n_resultados,
                    puntuacion   = :puntuacion,
                    fuente       = :fuente,
                    titulo       = :titulo,
                    util         = :util,
                    comentario   = :comentario,
                    estado       = :estado
                WHERE uuid_item = :w_uuid_item";
            $aDatos['w_uuid_item'] = $uuid_item;
        } else {
            $sql = "INSERT INTO $nom_tabla
                (uuid_item, f_consulta, pregunta, esquema, pantalla, n_resultados, puntuacion, fuente, titulo, util, comentario, estado)
                VALUES
                (:uuid_item, :f_consulta, :pregunta, :esquema, :pantalla, :n_resultados, :puntuacion, :fuente, :titulo, :util, :comentario, :estado)";
        }

        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    public function Eliminar(AyudaConsulta $ayudaConsulta): bool
    {
        $sql = 'DELETE FROM ' . $this->getNomTabla() . ' WHERE uuid_item = :w_uuid_item';

        return $this->prepareAndExecute(
            $this->getoDbl(),
            $sql,
            [':w_uuid_item' => $ayudaConsulta->getUuid_item()->value()],
            __METHOD__,
            __FILE__,
            __LINE__
        ) !== false;
    }

    public function purgarAnterioresA(DateTimeLocal $fecha): int
    {
        $sql = 'DELETE FROM ' . $this->getNomTabla() . '
            WHERE f_consulta < :fecha
              AND NOT (util IS FALSE AND estado = :pendiente)';
        $stmt = $this->prepareAndExecute(
            $this->getoDbl(),
            $sql,
            [
                ':fecha' => (new ConverterDate('timestamp', $fecha))->toPg(),
                ':pendiente' => AyudaConsultaEstado::PENDIENTE,
            ],
            __METHOD__,
            __FILE__,
            __LINE__
        );

        return $stmt === false ? 0 : $stmt->rowCount();
    }

    /**
     * Fila de PostgreSQL → tipos de PHP que espera la entidad.
     *
     * @param array<mixed> $aDatos
     * @return array<string, mixed>
     */
    private function hidratar(array $aDatos): array
    {
        $aDatos['f_consulta'] = (new ConverterDate('timestamp', $aDatos['f_consulta'] ?? null))->fromPg();
        $aDatos['util'] = ($aDatos['util'] ?? null) === null ? null : FuncTablasSupport::isTrue($aDatos['util']);

        $row = [];
        foreach ($aDatos as $key => $value) {
            $row[(string) $key] = $value;
        }

        return $row;
    }
}
