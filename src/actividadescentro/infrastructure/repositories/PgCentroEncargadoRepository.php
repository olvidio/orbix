<?php

namespace src\actividadescentro\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use PDO;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadescentro\domain\entity\CentroEncargado;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroEllas;


/**
 * Clase que adapta la tabla da_ctr_encargados a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
class PgCentroEncargadoRepository extends ClaseRepository implements CentroEncargadoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('da_ctr_encargados');
    }

    /**
     * retorna un texto con los dias que faltan para la siguiente actividad a partir de la fecha
     *     que se le pasa como parámetro. (o en negativo para una actividad anterior).
     *
     * @param integer id_ubi.
     * @param string iso. fecha de referencia respecto a la que calcular la diferencia de dias.
     * @return string dias de diferencia con la próxima/anterior actividad.
     */
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

        $txt_dif = "";
        foreach ($stmt as $aDades) {
            $txt_dif .= " " . $aDades['dif'] . ";";
        }
        return $txt_dif;
    }

    /**
     * retorna l'array d'objectes de tipus Actividad
     *
     * @param integer id_ubi.
     * @param string condicion a añadir (sin where): f_ini BETWEEN '1/1/2010' AND '1/8/2010'.
     * @return array|false
     */
    public function getActividadesDeCentros(int $iid_ubi, string $scondicion = ''):array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oActividadSet = new Set();

        if (!empty($scondicion)) $scondicion = ' AND ' . $scondicion;
        $sQuery = "SELECT d.id_activ 
                        FROM $nom_tabla d JOIN a_actividades_dl a USING (id_activ) 
                        WHERE d.id_ubi=$iid_ubi $scondicion 
                        ORDER BY f_ini";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $ActividadDlRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        foreach ($stmt as $aDades) {
            $id_activ = $aDades['id_activ'];
            $oActividad = $ActividadDlRepository->finfById($id_activ);
            $oActividadSet->add($oActividad);
        }
        return $oActividadSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Ubi
     *
     * @param integer id_actividad.
     * @return array|false
     */
    public function getCentrosEncargadosActividad(int $iid_activ):array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT * FROM $nom_tabla d WHERE id_activ=$iid_activ ORDER BY num_orden";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $oUbiSet = new Set();
        foreach ($stmt as $aDatos) {
            $id_ubi = $aDatos['id_ubi'];
            $sfsv = (int)substr($id_ubi, 0, 1);
            if (ConfigGlobal::mi_sfsv() === $sfsv) {
                $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
                $oUbi = $CentroDlRepository->findById($id_ubi);
            } else {
                $oUbi = CentroEllas::fromArray($aDatos);
            }
            $oUbiSet->add($oUbi);
        }
        return $oUbiSet->getTot();
    }


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CentroEncargado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CentroEncargado
     */
    public function getCentrosEncargados(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CentroEncargadoSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $CentroEncargado = CentroEncargado::fromArray($aDatos);
            $CentroEncargadoSet->add($CentroEncargado);
        }
        return $CentroEncargadoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CentroEncargado $CentroEncargado): bool
    {
        $id_activ = $CentroEncargado->getId_activ();
        $id_ubi = $CentroEncargado->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CentroEncargado $CentroEncargado): bool
    {
        $id_activ = $CentroEncargado->getId_activ();
        $id_ubi = $CentroEncargado->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_ubi);

        $aDatos = $CentroEncargado->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            unset($aDatos['id_ubi']);
            $update = "
					num_orden                = :num_orden,
					encargo                  = :encargo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_activ,id_ubi,num_orden,encargo)";
            $valores = "(:id_activ,:id_ubi,:num_orden,:encargo)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);    }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ, int $id_ubi): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_activ
     * @return array|bool
     */
    public function datosById(int $id_activ, int $id_ubi): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ AND id_ubi = $id_ubi";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ, int $id_ubi): ?CentroEncargado
    {
        $aDatos = $this->datosById($id_activ, $id_ubi);
        if (empty($aDatos)) {
            return null;
        }
        return CentroEncargado::fromArray($aDatos);
    }
}