<?php

namespace src\pasarela\infrastructure\persistence\postgresql;

use PDO;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterJson;
use src\pasarela\domain\criteria\PasarelaConfigField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgPasarelaConfigRepository extends ClaseRepository implements PasarelaConfigRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_Select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('pasarela_dl');
    }

    /**
     * Configuraciones de pasarela que cumplen el criterio.
     *
     * @return list<PasarelaConfig>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(PasarelaConfigField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['json_valor'] = (new ConverterJson($aDatos['json_valor'], false))->fromPg();
            $result[] = PasarelaConfig::fromArray($aDatos);
        }

        return $result;
    }

    public function Eliminar(PasarelaConfig $PasarelaConfig): bool
    {
        $nom_parametro = $PasarelaConfig->getNomParametroVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE nom_parametro = '$nom_parametro'";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(PasarelaConfig $PasarelaConfig): bool
    {
        $nom_parametro = $PasarelaConfig->getNomParametroVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($nom_parametro);

        $aDatos = $PasarelaConfig->toArrayForDatabase([
            'json_valor' => fn($v) => (new ConverterJson($v, false))->toPg(false),
        ]);

        if ($bInsert === false) {
            unset($aDatos['nom_parametro']);
            $update = '
					json_valor              = :json_valor';
            $sql = "UPDATE $nom_tabla SET $update WHERE nom_parametro = '$nom_parametro'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(nom_parametro,json_valor)';
            $valores = '(:nom_parametro,:json_valor)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $nom_parametro): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE nom_parametro = '$nom_parametro'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return true;
        }

        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(string $nom_parametro): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE nom_parametro = '$nom_parametro'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return false;
        }
        /** @var array<string, mixed> $row */

        return $row;
    }

    public function findById(string $nom_parametro): ?PasarelaConfig
    {
        $aDatos = $this->datosById($nom_parametro);
        if ($aDatos === false) {
            return null;
        }

        return PasarelaConfig::fromArray($aDatos);
    }
}
