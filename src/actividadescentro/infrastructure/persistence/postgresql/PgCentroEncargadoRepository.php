<?php

namespace src\actividadescentro\infrastructure\persistence\postgresql;

use PDO;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\actividadescentro\domain\value_objects\CentroEncargadoPk;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\actividadescentro\domain\criteria\CentroEncargadoField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;

/**
 * Clase que adapta la tabla da_ctr_encargados a la interfaz del repositorio
 */
class PgCentroEncargadoRepository extends ClaseRepository implements CentroEncargadoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct(
        private ActividadDlRepositoryInterface $actividadDlRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private CentroEllosRepositoryInterface $centroEllosRepository,
    ) {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('da_ctr_encargados');
    }

    public function getProximasActividadesDeCentro(int $id_ubi, string $f_ini_act_iso): string
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT nom_activ,f_ini,f_fin,(f_ini - date '" . $f_ini_act_iso . "') as dif
				FROM a_actividades_dl a JOIN $nom_tabla e USING (id_activ)
				WHERE e.id_ubi=$id_ubi
				ORDER BY abs(f_ini - date '" . $f_ini_act_iso . "')
				limit 3
				";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return '';
        }

        $txt_dif = '';
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !array_key_exists('dif', $aDades)) {
                continue;
            }
            $txt_dif .= ' ' . (string) $aDades['dif'] . ';';
        }
        return $txt_dif;
    }

    /**
     * @return list<ActividadAll>
     */
    public function getActividadesDeCentros(int $iid_ubi, string $scondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oActividadSet = new Set();

        if ($scondicion !== '') {
            $scondicion = ' AND ' . $scondicion;
        }
        $sQuery = "SELECT d.id_activ
                        FROM $nom_tabla d JOIN a_actividades_dl a USING (id_activ)
                        WHERE d.id_ubi=$iid_ubi $scondicion
                        ORDER BY f_ini";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !array_key_exists('id_activ', $aDades) || !is_numeric($aDades['id_activ'])) {
                continue;
            }
            $id_activ = (int) $aDades['id_activ'];
            $oActividad = $this->actividadDlRepository->findById($id_activ);
            if ($oActividad !== null) {
                $oActividadSet->add($oActividad);
            }
        }
        /** @var list<ActividadAll> $result */
        $result = array_values($oActividadSet->getTot());

        return $result;
    }

    /**
     * @return list<CentroDl|CentroEllas|CentroEllos>
     */
    public function getCentrosEncargadosActividad(int $iid_activ): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT * FROM $nom_tabla d WHERE id_activ=$iid_activ ORDER BY num_orden";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $oUbiSet = new Set();
        foreach ($stmt as $aDatos) {
            if (!is_array($aDatos) || !array_key_exists('id_ubi', $aDatos) || !is_numeric($aDatos['id_ubi'])) {
                continue;
            }
            $id_ubi = (int) $aDatos['id_ubi'];
            $sfsv = (int) substr((string) $id_ubi, 0, 1);
            if (ConfigGlobal::mi_sfsv() === $sfsv) {
                // En DMZ no existe u_centros_dl (sv); centros vía comun.
                $oUbi = ConfigGlobal::is_dmz()
                    ? $this->centroEllosRepository->findById($id_ubi)
                    : $this->centroDlRepository->findById($id_ubi);
            } else {
                $oUbi = $this->centroEllasRepository->findById($id_ubi);
            }
            if ($oUbi !== null) {
                $oUbiSet->add($oUbi);
            }
        }
        /** @var list<CentroDl|CentroEllas|CentroEllos> $result */
        $result = array_values($oUbiSet->getTot());

        return $result;
    }

    /**
     * CentroEncargado que cumplen el criterio.
     *
     * @return list<CentroEncargado>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(CentroEncargadoField::class, self::COLUMNAS);
        return array_map(CentroEncargado::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<CentroEncargado>
     */
    public function getCentrosEncargados(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(CentroEncargadoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    public function Eliminar(CentroEncargado $CentroEncargado, bool $registrarCambios = true): bool
    {
        $id_activ = $CentroEncargado->getId_activ();
        $id_ubi = $CentroEncargado->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(CentroEncargado $CentroEncargado, bool $registrarCambios = true): bool
    {
        $id_activ = $CentroEncargado->getId_activ();
        $id_ubi = $CentroEncargado->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_ubi);

        $aDatos = $CentroEncargado->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_activ']);
            unset($aDatos['id_ubi']);
            $update = "
					num_orden                = :num_orden,
					encargo                  = :encargo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_activ,id_ubi,num_orden,encargo)';
            $valores = '(:id_activ,:id_ubi,:num_orden,:encargo)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ, int $id_ubi): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
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
    public function datosById(int $id_activ, int $id_ubi): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(CentroEncargadoPk $pk): array|false
    {
        return $this->datosById($pk->IdActiv(), $pk->IdUbi());
    }

    public function findById(int $id_activ, int $id_ubi): ?CentroEncargado
    {
        $aDatos = $this->datosById($id_activ, $id_ubi);
        if ($aDatos === false || $aDatos === []) {
            return null;
        }
        return CentroEncargado::fromArray($aDatos);
    }

    public function findByPk(CentroEncargadoPk $pk): ?CentroEncargado
    {
        return $this->findById($pk->IdActiv(), $pk->IdUbi());
    }
}
