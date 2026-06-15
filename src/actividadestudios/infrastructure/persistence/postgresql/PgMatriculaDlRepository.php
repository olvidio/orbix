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

    protected function getNomTablaForWrite(Matricula $Matricula): string
    {
        if (!$this->isRegionStgrSession()) {
            return $this->getNomTabla();
        }

        $this->resolveDlSchema($Matricula);

        return 'd_matriculas_activ_dl';
    }

    protected function getoDblForWrite(Matricula $Matricula): PDO
    {
        if (!$this->isRegionStgrSession()) {
            return $this->getoDbl();
        }

        $schema = $this->resolveDlSchema($Matricula);

        return $this->writePdoForSchema($schema);
    }

    private function resolveDlSchema(Matricula $Matricula): string
    {
        $idDl = $Matricula->getId_dl();
        if ($idDl === null) {
            throw new RuntimeException(_('Falta id_dl para modificar la matrícula en esquema región STGR.'));
        }

        return $this->schemaFromIdDl($idDl);
    }

    private function schemaFromIdDl(int $idDl): string
    {
        if (isset($this->schemaByIdDl[$idDl])) {
            return $this->schemaByIdDl[$idDl];
        }

        $schemas = $this->delegacionRepository->getArraySchemasRegionStgr(
            ConfigGlobal::mi_region(),
            ConfigGlobal::mi_sfsv(),
        );
        if (!isset($schemas[$idDl])) {
            throw new RuntimeException(sprintf(_('No encuentro esquema para id_dl %s'), (string) $idDl));
        }

        $schema = (string) $schemas[$idDl];
        $this->schemaByIdDl[$idDl] = $schema;

        return $schema;
    }

    /**
     * @param array<string, mixed> $normalized
     */
    protected function eliminarFilaRaw(array $normalized): bool
    {
        if (!$this->isRegionStgrSession()) {
            return parent::eliminarFilaRaw($normalized);
        }

        if (!isset($normalized['id_activ'], $normalized['id_asignatura'], $normalized['id_nom'], $normalized['id_dl'])) {
            return false;
        }

        $idDl = (int) $normalized['id_dl'];
        if ($idDl <= 0) {
            return false;
        }

        $id_activ = (int) $normalized['id_activ'];
        $id_asignatura = (int) $normalized['id_asignatura'];
        $id_nom = (int) $normalized['id_nom'];
        $schema = $this->schemaFromIdDl($idDl);
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
