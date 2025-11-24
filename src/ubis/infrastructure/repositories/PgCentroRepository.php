<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use PDOException;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Centro;
use function core\is_true;


/**
 * Clase que adapta la tabla u_centros a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/11/2025
 */
class PgCentroRepository extends ClaseRepository implements CentroRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('u_centros');
    }

    public function getArrayCentrosCdc(string $condicion = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        //$orden = 'nombre_ubi';

        $sWhere = "WHERE status = 't' AND cdc='t' ";
        if (!empty($condicion)) {
            $sWhere .= 'AND ' . $condicion;
        }
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sWhere
				";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCentroCdc.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $a_ctr = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $a_ctr[$id_ubi] = $nombre_ubi;
        }

        return $a_ctr;
    }

    public function getArrayCentros(string $condicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $orden = 'nombre_ubi';
        if (empty($condicion)) $condicion = "WHERE status = 't'";
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$condicion
				ORDER BY $orden";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCentro.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aCentros = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $aCentros[$id_ubi] = $nombre_ubi;
        }

        return $aCentros;
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Centro
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Centro
     */
    public function getCentros(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CentroSet = new Set();
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
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClaveError = 'PgCentroRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgCentroRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['f_status'] = (new ConverterDate('date', $aDatos['f_status']))->fromPg();
            $Centro = new Centro();
            $Centro->setAllAttributes($aDatos);
            $CentroSet->add($Centro);
        }
        return $CentroSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Centro $Centro): bool
    {
        $id_ubi = $Centro->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi = $id_ubi")) === FALSE) {
            $sClaveError = 'PgCentroRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Centro $Centro): bool
    {
        $id_ubi = $Centro->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_ubi);

        $aDatos = [];
        $aDatos['tipo_ubi'] = $Centro->getTipo_ubi();
        $aDatos['nombre_ubi'] = $Centro->getNombre_ubi();
        $aDatos['dl'] = $Centro->getDl();
        $aDatos['pais'] = $Centro->getPais();
        $aDatos['region'] = $Centro->getRegion();
        $aDatos['status'] = $Centro->isStatus();
        $aDatos['sv'] = $Centro->isSv();
        $aDatos['sf'] = $Centro->isSf();
        $aDatos['tipo_ctr'] = $Centro->getTipo_ctr();
        $aDatos['tipo_labor'] = $Centro->getTipo_labor();
        $aDatos['cdc'] = $Centro->isCdc();
        $aDatos['id_ctr_padre'] = $Centro->getId_ctr_padre();
        // para las fechas
        $aDatos['f_status'] = (new ConverterDate('date', $Centro->getF_status()))->toPg();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['status'])) {
            $aDatos['status'] = 'true';
        } else {
            $aDatos['status'] = 'false';
        }
        if (is_true($aDatos['sv'])) {
            $aDatos['sv'] = 'true';
        } else {
            $aDatos['sv'] = 'false';
        }
        if (is_true($aDatos['sf'])) {
            $aDatos['sf'] = 'true';
        } else {
            $aDatos['sf'] = 'false';
        }
        if (is_true($aDatos['cdc'])) {
            $aDatos['cdc'] = 'true';
        } else {
            $aDatos['cdc'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					status                   = :status,
					f_status                 = :f_status,
					sv                       = :sv,
					sf                       = :sf,
					tipo_ctr                 = :tipo_ctr,
					tipo_labor               = :tipo_labor,
					cdc                      = :cdc,
					id_ctr_padre             = :id_ctr_padre";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi = $id_ubi")) === FALSE) {
                $sClaveError = 'PgCentroRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgCentroRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            // INSERT
            $aDatos['id_ubi'] = $Centro->getId_ubi();
            $campos = "(tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre)";
            $valores = "(:tipo_ubi,:id_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc,:id_ctr_padre)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgCentroRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgCentroRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_ubi): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi")) === FALSE) {
            $sClaveError = 'PgCentroRepository.isNew';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_ubi
     * @return array|bool
     */
    public function datosById(int $id_ubi): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi")) === FALSE) {
            $sClaveError = 'PgCentroRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== FALSE) {
            $aDatos['f_status'] = (new ConverterDate('date', $aDatos['f_status']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_ubi en la base de datos .
     */
    public function findById(int $id_ubi): ?Centro
    {
        $aDatos = $this->datosById($id_ubi);
        if (empty($aDatos)) {
            return null;
        }
        return (new Centro())->setAllAttributes($aDatos);
    }
}