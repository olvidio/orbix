<?php

namespace src\inventario\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use PDOException;
use src\shared\traits\HandlesPdoErrors;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\value_objects\DocumentoId;
use function core\is_true;

/**
 * Clase que adapta la tabla i_documentos_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgDocumentoRepository extends ClaseRepository implements DocumentoRepositoryInterface
{
    use HandlesPdoErrors;
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_documentos_dl');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Documento
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Documento
     */
    public function getDocumentos(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $DocumentoSet = new Set();
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
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute( $oDbl, $sQry, $aWhere,__METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
            $aDatos['f_asignado'] = (new ConverterDate('date', $aDatos['f_asignado']))->fromPg();
            $aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $aDatos['f_ult_comprobacion']))->fromPg();
            $aDatos['f_perdido'] = (new ConverterDate('date', $aDatos['f_perdido']))->fromPg();
            $aDatos['f_eliminado'] = (new ConverterDate('date', $aDatos['f_eliminado']))->fromPg();
            $Documento = new Documento();
            $Documento->setAllAttributes($aDatos);
            $DocumentoSet->add($Documento);
        }
        return $DocumentoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Documento $Documento): bool
    {
        $id_doc = $Documento->getIdDocVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_doc = $id_doc";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Documento $Documento): bool
    {
        $id_doc = $Documento->getIdDocVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_doc);

        $aDatos = [];
        $aDatos['id_tipo_doc'] = $Documento->getIdTipoDocVo()->value();
        $aDatos['id_ubi'] = $Documento->getIdUbiVo()->value();
        $aDatos['id_lugar'] = $Documento->getIdLugarVo()?->value();
        $aDatos['observ'] = $Documento->getObservVo()?->value();
        $aDatos['observ_ctr'] = $Documento->getObservCtrVo()?->value();
        $aDatos['en_busqueda'] = $Documento->getEnBusquedaVo()?->value();
        $aDatos['perdido'] = $Documento->getPerdidoVo()?->value();
        $aDatos['eliminado'] = $Documento->getEliminadoVo()?->value();
        $aDatos['num_reg'] = $Documento->getNumRegVo()?->value();
        $aDatos['num_ini'] = $Documento->getNumIniVo()?->value();
        $aDatos['num_fin'] = $Documento->getNumFinVo()?->value();
        $aDatos['identificador'] = $Documento->getIdentificadorVo()?->value();
        $aDatos['num_ejemplares'] = $Documento->getNumEjemplaresVo()?->value();
        // para las fechas (se mantienen utilitarios)
        $aDatos['f_recibido'] = (new ConverterDate('date', $Documento->getF_recibido()))->toPg();
        $aDatos['f_asignado'] = (new ConverterDate('date', $Documento->getF_asignado()))->toPg();
        $aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $Documento->getF_ult_comprobacion()))->toPg();
        $aDatos['f_perdido'] = (new ConverterDate('date', $Documento->getF_perdido()))->toPg();
        $aDatos['f_eliminado'] = (new ConverterDate('date', $Documento->getF_eliminado()))->toPg();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['en_busqueda'])) {
            $aDatos['en_busqueda'] = 'true';
        } else {
            $aDatos['en_busqueda'] = 'false';
        }
        if (is_true($aDatos['perdido'])) {
            $aDatos['perdido'] = 'true';
        } else {
            $aDatos['perdido'] = 'false';
        }
        if (is_true($aDatos['eliminado'])) {
            $aDatos['eliminado'] = 'true';
        } else {
            $aDatos['eliminado'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
                    id_tipo_doc              = :id_tipo_doc,
                    id_ubi                   = :id_ubi,
                    id_lugar                 = :id_lugar,
                    f_recibido               = :f_recibido,
                    f_asignado               = :f_asignado,
                    observ                   = :observ,
                    observ_ctr               = :observ_ctr,
                    f_ult_comprobacion       = :f_ult_comprobacion,
                    en_busqueda              = :en_busqueda,
                    perdido                  = :perdido,
                    f_perdido                = :f_perdido,
                    eliminado                = :eliminado,
                    f_eliminado              = :f_eliminado,
                    num_reg                  = :num_reg,
                    num_ini                  = :num_ini,
                    num_fin                  = :num_fin,
                    identificador            = :identificador,
                    num_ejemplares           = :num_ejemplares";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_doc = $id_doc";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
         //INSERT
            $aDatos['id_doc'] = $Documento->getIdDocVo()->value();
            $campos = "(id_doc,id_tipo_doc,id_ubi,id_lugar,f_recibido,f_asignado,observ,observ_ctr,f_ult_comprobacion,en_busqueda,perdido,f_perdido,eliminado,f_eliminado,num_reg,num_ini,num_fin,identificador,num_ejemplares)";
            $valores = "(:id_doc,:id_tipo_doc,:id_ubi,:id_lugar,:f_recibido,:f_asignado,:observ,:observ_ctr,:f_ult_comprobacion,:en_busqueda,:perdido,:f_perdido,:eliminado,:f_eliminado,:num_reg,:num_ini,:num_fin,:identificador,:num_ejemplares)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		}
		return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_doc): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_doc = $id_doc";
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
     * @param DocumentoId $id_doc
     * @return array|bool
     */
    public function datosById(int $id_doc): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_doc = $id_doc";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
            $aDatos['f_asignado'] = (new ConverterDate('date', $aDatos['f_asignado']))->fromPg();
            $aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $aDatos['f_ult_comprobacion']))->fromPg();
            $aDatos['f_perdido'] = (new ConverterDate('date', $aDatos['f_perdido']))->fromPg();
            $aDatos['f_eliminado'] = (new ConverterDate('date', $aDatos['f_eliminado']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_doc en la base de datos .
     */
    public function findById(int $id_doc): ?Documento
    {
        $aDatos = $this->datosById($id_doc);
        if (empty($aDatos)) {
            return null;
        }
        return (new Documento())->setAllAttributes($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_documentos_dl_id_doc_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}