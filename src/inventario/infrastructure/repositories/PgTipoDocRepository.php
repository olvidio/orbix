<?php

namespace src\inventario\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\TipoDoc;
use function core\is_true;

/**
 * Clase que adapta la tabla i_tipo_documento_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgTipoDocRepository extends ClaseRepository implements TipoDocRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_tipo_documento_dl');
    }

    public function getArrayTipoDoc(): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_tipo_doc, CASE WHEN nom_doc IS NOT NULL THEN sigla ||' ('||nom_doc||')'
            ELSE sigla
       		END
	   		FROM $nom_tabla
			WHERE vigente = 't'
			ORDER BY sigla,nom_doc ";
        if ($oDbl->query($sQuery) === false) {
            $sClauError = 'GestorTipoDoc.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo TipoDoc
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TipoDoc
     */
    public function getTipoDocs(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $TipoDocSet = new Set();
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
            $sClaveError = 'PgTipoDocRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClaveError = 'PgTipoDocRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return false;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $TipoDoc = new TipoDoc();
            $TipoDoc->setAllAttributes($aDatos);
            $TipoDocSet->add($TipoDoc);
        }
        return $TipoDocSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoDoc $TipoDoc): bool
    {
        $id_tipo_doc = $TipoDoc->getIdTipoDocVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_doc = $id_tipo_doc")) === false) {
            $sClaveError = 'PgTipoDocRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoDoc $TipoDoc): bool
    {
        $id_tipo_doc = $TipoDoc->getIdTipoDocVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_doc);

        $aDatos = [];
        $aDatos['nom_doc'] = $TipoDoc->getNomDocVo()?->value();
        $aDatos['sigla'] = $TipoDoc->getSiglaVo()?->value();
        $aDatos['observ'] = $TipoDoc->getObservVo()?->value();
        $aDatos['id_coleccion'] = $TipoDoc->getIdColeccionVo()?->value();
        $aDatos['bajo_llave'] = $TipoDoc->getBajoLlaveVo()?->value();
        $aDatos['vigente'] = $TipoDoc->getVigenteVo()?->value();
        $aDatos['numerado'] = $TipoDoc->getNumeradoVo()->value();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['bajo_llave'])) {
            $aDatos['bajo_llave'] = 'true';
        } else {
            $aDatos['bajo_llave'] = 'false';
        }
        if (is_true($aDatos['vigente'])) {
            $aDatos['vigente'] = 'true';
        } else {
            $aDatos['vigente'] = 'false';
        }
        if (is_true($aDatos['numerado'])) {
            $aDatos['numerado'] = 'true';
        } else {
            $aDatos['numerado'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
                    nom_doc                  = :nom_doc,
                    sigla                    = :sigla,
                    observ                   = :observ,
                    id_coleccion             = :id_coleccion,
                    bajo_llave               = :bajo_llave,
                    vigente                  = :vigente,
                    numerado                 = :numerado";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_doc = $id_tipo_doc")) === false) {
                $sClaveError = 'PgTipoDocRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgTipoDocRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        } else {
            // INSERT
            $aDatos['id_tipo_doc'] = $TipoDoc->getIdTipoDocVo()->value();
            $campos = "(id_tipo_doc,nom_doc,sigla,observ,id_coleccion,bajo_llave,vigente,numerado)";
            $valores = "(:id_tipo_doc,:nom_doc,:sigla,:observ,:id_coleccion,:bajo_llave,:vigente,:numerado)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClaveError = 'PgTipoDocRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgTipoDocRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_tipo_doc): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_doc = $id_tipo_doc")) === false) {
            $sClaveError = 'PgTipoDocRepository.isNew';
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
     * @param int $id_tipo_doc
     * @return array|bool
     */
    public function datosById(int $id_tipo_doc): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_doc = $id_tipo_doc")) === false) {
            $sClaveError = 'PgTipoDocRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Busca la clase con id_tipo_doc en la base de datos .
     */
    public function findById(int $id_tipo_doc): ?TipoDoc
    {
        $aDatos = $this->datosById($id_tipo_doc);
        if (empty($aDatos)) {
            return null;
        }
        return (new TipoDoc())->setAllAttributes($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_tipo_documento_dl_id_tipo_doc_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}