<?php

namespace src\actividadestudios\infrastructure\persistence\postgresql;

use PDO;
use RuntimeException;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\Matricula;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;


/**
 * Clase que adapta la tabla d_matriculas_activ a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
class PgMatriculaDlRepository extends PgMatriculaRepository implements MatriculaDlRepositoryInterface
{
    use HandlesPdoErrors;

    /** @var array<string, PDO> */
    private array $writePdoBySchema = [];

    /** @var array<int, string> */
    private array $schemaByIdDl = [];

    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
    ) {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_matriculas_activ_dl');
    }

    public function Eliminar(Matricula $Matricula): bool
    {
        if (!$this->isRegionStgrSession()) {
            return parent::Eliminar($Matricula);
        }

        $schema = $this->resolveSchemaForMatricula($Matricula);
        if ($schema === null) {
            error_log(sprintf(
                '[PgMatriculaDlRepository] No se borra matrícula id_activ=%d id_asignatura=%d id_nom=%d: sin esquema DL',
                $Matricula->getId_activ(),
                $Matricula->getIdAsignaturaVo()->value(),
                $Matricula->getId_nom(),
            ));

            return false;
        }

        $id_activ = $Matricula->getId_activ();
        $id_asignatura = $Matricula->getIdAsignaturaVo()->value();
        $id_nom = $Matricula->getId_nom();
        $oDbl = $this->writePdoForSchema($schema);
        $sql = "DELETE FROM d_matriculas_activ_dl WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    protected function getNomTablaForWrite(Matricula $Matricula): string
    {
        if (!$this->isRegionStgrSession()) {
            return $this->getNomTabla();
        }

        $this->requireSchemaForMatricula($Matricula);

        return 'd_matriculas_activ_dl';
    }

    protected function getoDblForWrite(Matricula $Matricula): PDO
    {
        if (!$this->isRegionStgrSession()) {
            return $this->getoDbl();
        }

        $schema = $this->requireSchemaForMatricula($Matricula);

        return $this->writePdoForSchema($schema);
    }

    private function requireSchemaForMatricula(Matricula $Matricula): string
    {
        $schema = $this->resolveSchemaForMatricula($Matricula);
        if ($schema === null) {
            throw new RuntimeException(_('Falta id_dl para modificar la matrícula en esquema región STGR.'));
        }

        return $schema;
    }

    private function resolveSchemaForMatricula(Matricula $Matricula): ?string
    {
        $idDl = $Matricula->getId_dl();
        if ($idDl !== null && $idDl > 0) {
            return $this->schemaFromIdDl($idDl);
        }

        return $this->resolveSchemaFromIdNom($Matricula->getId_nom());
    }

    private function schemaFromIdDl(int $idDl): ?string
    {
        if ($idDl <= 0) {
            return null;
        }

        if (isset($this->schemaByIdDl[$idDl])) {
            return $this->schemaByIdDl[$idDl];
        }

        $schemas = $this->delegacionRepository->getArraySchemasRegionStgr(
            ConfigGlobal::mi_region(),
            ConfigGlobal::mi_sfsv(),
        );
        if (!isset($schemas[$idDl])) {
            return null;
        }

        $schema = (string) $schemas[$idDl];
        $this->schemaByIdDl[$idDl] = $schema;

        return $schema;
    }

    private function resolveSchemaFromIdNom(int $idNom): ?string
    {
        if ($idNom === 0) {
            return null;
        }

        $oDbl = $this->getoDbl();
        $sql = 'SELECT dl FROM personas_dl WHERE id_nom = :id_nom LIMIT 1';
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false || !$this->PdoExecute($stmt, ['id_nom' => $idNom], __METHOD__, __FILE__, __LINE__)) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row) || !isset($row['dl'])) {
            return null;
        }

        $dlPersona = is_scalar($row['dl']) ? (string) $row['dl'] : '';
        if ($dlPersona === '') {
            return null;
        }

        $schemas = $this->delegacionRepository->getArraySchemasRegionStgr(
            ConfigGlobal::mi_region(),
            ConfigGlobal::mi_sfsv(),
        );
        foreach ($schemas as $schema) {
            $schemaName = (string) $schema;
            $parts = explode('-', $schemaName, 2);
            if (count($parts) < 2) {
                continue;
            }
            if ($parts[1] === $dlPersona) {
                return $schemaName;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $normalized
     */
    protected function eliminarFilaRaw(array $normalized): bool
    {
        if (!$this->isRegionStgrSession()) {
            return parent::eliminarFilaRaw($normalized);
        }

        if (!isset($normalized['id_activ'], $normalized['id_asignatura'], $normalized['id_nom'])) {
            return false;
        }

        $schema = null;
        $idDl = isset($normalized['id_dl']) && is_numeric($normalized['id_dl']) ? (int) $normalized['id_dl'] : 0;
        if ($idDl > 0) {
            $schema = $this->schemaFromIdDl($idDl);
        }
        if ($schema === null) {
            $schema = $this->resolveSchemaFromIdNom(is_numeric($normalized['id_nom']) ? (int) $normalized['id_nom'] : 0);
        }
        if ($schema === null) {
            return false;
        }

        $id_activ = is_numeric($normalized['id_activ']) ? (int) $normalized['id_activ'] : 0;
        $id_asignatura = is_numeric($normalized['id_asignatura']) ? (int) $normalized['id_asignatura'] : 0;
        $id_nom = is_numeric($normalized['id_nom']) ? (int) $normalized['id_nom'] : 0;
        $oDbl = $this->writePdoForSchema($schema);
        $sql = "DELETE FROM d_matriculas_activ_dl WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura AND id_nom=$id_nom";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    private function writePdoForSchema(string $schema): PDO
    {
        if (isset($this->writePdoBySchema[$schema])) {
            return $this->writePdoBySchema[$schema];
        }

        $db = ConfigGlobal::mi_sfsv() === 1 ? 'sv' : 'sf';
        $oConfigDB = new ConfigDB($db);
        $config = $oConfigDB->getEsquema($schema);
        $this->writePdoBySchema[$schema] = (new DBConnection($config))->getPDO();

        return $this->writePdoBySchema[$schema];
    }

    private function isRegionStgrSession(): bool
    {
        return ConfigGlobal::mi_region() === ConfigGlobal::mi_delef();
    }

}
