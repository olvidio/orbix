<?php

namespace src\inventario\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\value_objects\DocumentoId;
use src\shared\traits\HandlesPdoErrors;
use function src\shared\domain\helpers\is_true;

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
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_documentos_dl');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Documento
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Documento> Una colección de objetos de tipo Documento
     */
    public function getDocumentos(array $aWhere = [], array $aOperators = []): array
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
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos = $this->normalizeAssocRow($aDatos);
            // para las fechas del postgres (texto iso)
            $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
            $aDatos['f_asignado'] = (new ConverterDate('date', $aDatos['f_asignado']))->fromPg();
            $aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $aDatos['f_ult_comprobacion']))->fromPg();
            $aDatos['f_perdido'] = (new ConverterDate('date', $aDatos['f_perdido']))->fromPg();
            $aDatos['f_eliminado'] = (new ConverterDate('date', $aDatos['f_eliminado']))->fromPg();
            $Documento = Documento::fromArray($aDatos);
            $DocumentoSet->add($Documento);
        }
        /** @var list<Documento> $result */
        $result = array_values($DocumentoSet->getTot());
        return $result;
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

        $aDatos = $Documento->toArrayForDatabase([
            'f_recibido' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_asignado' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_ult_comprobacion' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_perdido' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_eliminado' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        /*
        $aDatos = [];
        $aDatos['id_tipo_doc'] = $Documento->getIdTipoDocVo()->value();
        $aDatos['id_ubi'] = $Documento->getId_ubi();
        $aDatos['id_lugar'] = $Documento->getIdLugarVo()?->value();
        $aDatos['observ'] = $Documento->getObservVo()?->value();
        $aDatos['observ_ctr'] = $Documento->getObservCtrVo()?->value();
        $aDatos['en_busqueda'] = $Documento->isEn_busqueda();
        $aDatos['perdido'] = $Documento->isPerdido();
        $aDatos['eliminado'] = $Documento->isEliminado();
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
        array_walk($aDatos, 'src\shared\domain\helpers\poner_null');
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
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_doc']);
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
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_doc,id_tipo_doc,id_ubi,id_lugar,f_recibido,f_asignado,observ,observ_ctr,f_ult_comprobacion,en_busqueda,perdido,f_perdido,eliminado,f_eliminado,num_reg,num_ini,num_fin,identificador,num_ejemplares)";
            $valores = "(:id_doc,:id_tipo_doc,:id_ubi,:id_lugar,:f_recibido,:f_asignado,:observ,:observ_ctr,:f_ult_comprobacion,:en_busqueda,:perdido,:f_perdido,:eliminado,:f_eliminado,:num_reg,:num_ini,:num_fin,:identificador,:num_ejemplares)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
    }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_doc): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_doc = $id_doc";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_doc
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_doc): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_doc = $id_doc";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        // para las fechas del postgres (texto iso)
        $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
        $aDatos['f_asignado'] = (new ConverterDate('date', $aDatos['f_asignado']))->fromPg();
        $aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $aDatos['f_ult_comprobacion']))->fromPg();
        $aDatos['f_perdido'] = (new ConverterDate('date', $aDatos['f_perdido']))->fromPg();
        $aDatos['f_eliminado'] = (new ConverterDate('date', $aDatos['f_eliminado']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_doc en la base de datos .
     */
    public function findById(int $id_doc): ?Documento
    {
        $aDatos = $this->datosById($id_doc);
        if ($aDatos === false) {
            return null;
        }
        return Documento::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_documentos_dl_id_doc_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}