<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use PDOException;
use src\ubis\domain\contracts\DireccionRepositoryInterface;
use src\ubis\domain\entity\Direccion;
use function core\is_true;

/**
 * Clase que adapta la tabla u_dir_ctr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class PgDireccionRepository extends ClaseRepository implements DireccionRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $oDbl_Select = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_dir_ctr');
    }

    public function getArrayPoblaciones($sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT DISTINCT initcap(poblacion), initcap(poblacion)
				FROM $nom_tabla
				$sCondicion
				ORDER BY initcap(poblacion)";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDireccion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $poblacion = $row[0];
            $poblacion1 = $row[1];

            $aOpciones[$poblacion] = $poblacion1;
        }

        return $aOpciones;
    }

    public function getArrayPaises($sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT DISTINCT initcap(pais),initcap(pais)
				FROM $nom_tabla
				$sCondicion
				ORDER BY initcap(pais)";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDireccion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $pais = $row[0];
            $pais1 = $row[1];

            $aOpciones[$pais] = $pais1;
        }

        return $aOpciones;
    }
    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Direccion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Direccion
     */
    public function getDirecciones(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $DireccionSet = new Set();
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
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClaveError = 'PgDireccionRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClaveError = 'PgDireccionRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return false;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para los bytea: (resources)
            $handle = $aDatos['plano_doc'];
            if ($handle !== null) {
                $contents = stream_get_contents($handle);
                fclose($handle);
                $plano_doc = $contents;
                $aDatos['plano_doc'] = $plano_doc;
            }
            // para las fechas del postgres (texto iso)
            $aDatos['f_direccion'] = (new ConverterDate('date', $aDatos['f_direccion']))->fromPg();
            $Direccion = new Direccion();
            $Direccion->setAllAttributes($aDatos);
            $DireccionSet->add($Direccion);
        }
        return $DireccionSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Direccion $Direccion): bool
    {
        $id_direccion = $Direccion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_direccion = $id_direccion")) === false) {
            $sClaveError = 'PgDireccionRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Direccion $Direccion): bool
    {
        $id_direccion = $Direccion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_direccion);

        $aDatos = [];
        $aDatos['direccion'] = $Direccion->getDireccion();
        $aDatos['c_p'] = $Direccion->getC_p();
        $aDatos['poblacion'] = $Direccion->getPoblacion();
        $aDatos['provincia'] = $Direccion->getProvincia();
        $aDatos['a_p'] = $Direccion->getA_p();
        $aDatos['pais'] = $Direccion->getPais();
        $aDatos['observ'] = $Direccion->getObserv();
        $aDatos['cp_dcha'] = $Direccion->isCp_dcha();
        $aDatos['latitud'] = $Direccion->getLatitud();
        $aDatos['longitud'] = $Direccion->getLongitud();
        $aDatos['plano_extension'] = $Direccion->getPlano_extension();
        $aDatos['plano_nom'] = $Direccion->getPlano_nom();
        $aDatos['nom_sede'] = $Direccion->getNom_sede();
        // para los bytea
        $aDatos['plano_doc'] = bin2hex($Direccion->getPlano_doc());
        // para las fechas
        $aDatos['f_direccion'] = (new ConverterDate('date', $Direccion->getF_direccion()))->toPg();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['cp_dcha'])) {
            $aDatos['cp_dcha'] = 'true';
        } else {
            $aDatos['cp_dcha'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					direccion                = :direccion,
					c_p                      = :c_p,
					poblacion                = :poblacion,
					provincia                = :provincia,
					a_p                      = :a_p,
					pais                     = :pais,
					f_direccion              = :f_direccion,
					observ                   = :observ,
					cp_dcha                  = :cp_dcha,
					latitud                  = :latitud,
					longitud                 = :longitud,
					plano_doc                = :plano_doc,
					plano_extension          = :plano_extension,
					plano_nom                = :plano_nom,
					nom_sede                 = :nom_sede";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_direccion = $id_direccion")) === false) {
                $sClaveError = 'PgDireccionRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDireccionRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        } else {
            // INSERT
            $aDatos['id_direccion'] = $Direccion->getId_direccion();
            $campos = "(id_direccion,direccion,c_p,poblacion,provincia,a_p,pais,f_direccion,observ,cp_dcha,latitud,longitud,plano_doc,plano_extension,plano_nom,nom_sede)";
            $valores = "(:id_direccion,:direccion,:c_p,:poblacion,:provincia,:a_p,:pais,:f_direccion,:observ,:cp_dcha,:latitud,:longitud,:plano_doc,:plano_extension,:plano_nom,:nom_sede)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClaveError = 'PgDireccionRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDireccionRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_direccion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_direccion = $id_direccion")) === false) {
            $sClaveError = 'PgDireccionRepository.isNew';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_direccion
     * @return array|bool
     */
    public function datosById(int $id_direccion): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_direccion = $id_direccion")) === false) {
            $sClaveError = 'PgDireccionRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        // para los bytea, sobre escribo los valores:
        $splano_doc = '';
        $oDblSt->bindColumn('plano_doc', $splano_doc, PDO::PARAM_STR);
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        if ($aDatos !== false) {
            $aDatos['plano_doc'] = hex2bin($splano_doc ?? '');
        }
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_direccion'] = (new ConverterDate('date', $aDatos['f_direccion']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_direccion en la base de datos .
     */
    public function findById(int $id_direccion): ?Direccion
    {
        $aDatos = $this->datosById($id_direccion);
        if (empty($aDatos)) {
            return null;
        }
        return (new Direccion())->setAllAttributes($aDatos);
    }
}