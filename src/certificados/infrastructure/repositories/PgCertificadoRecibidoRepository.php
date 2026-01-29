<?php

namespace src\certificados\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\shared\traits\HandlesPdoErrors;
use function core\is_true;

/**
 * Clase que adapta la tabla e_certificados_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
class PgCertificadoRecibidoRepository extends ClaseRepository implements CertificadoRecibidoRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_certificados_dl');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Certificado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Certificado
     */
    public function getCertificados(array $aWhere = [], array $aOperators = []): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CertificadoSet = new Set();
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
            // para los bytea: (resources)
            $handle = $aDatos['documento'];
            if ($handle !== null) {
                $contents = stream_get_contents($handle);
                fclose($handle);
                $documento = $contents;
                $aDatos['documento'] = $documento;
            }
            // para las fechas del postgres (texto iso)
            $aDatos['f_certificado'] = (new ConverterDate('date', $aDatos['f_certificado']))->fromPg();
            $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
            $Certificado =  CertificadoRecibido::fromArray($aDatos);
            $CertificadoSet->add($Certificado);
        }
        return $CertificadoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CertificadoRecibido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CertificadoRecibido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $Certificado->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_nom                   = :id_nom,
					nom                      = :nom,
					idioma                   = :idioma,
					destino                  = :destino,
					certificado              = :certificado,
					f_certificado            = :f_certificado,
					esquema_emisor           = :esquema_emisor,
					firmado                  = :firmado,
					documento                = :documento,
                    f_recibido                = :f_recibido";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_item,id_nom,nom,idioma,destino,certificado,f_certificado,esquema_emisor,firmado,documento,f_recibido)";
            $valores = "(:id_item,:id_nom,:nom,:idioma,:destino,:certificado,:f_certificado,:esquema_emisor,:firmado,:documento,:f_recibido)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($aDatos === false) {
            return false;
        }

        // para los bytea: (resources)
        $handle = $aDatos['documento'];
        if (is_resource($handle)) {
            $contents = stream_get_contents($handle);
            fclose($handle);
            $aDatos['documento'] = $contents;
        }

        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['f_certificado'] = (new ConverterDate('date', $aDatos['f_certificado']))->fromPg();
            $aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
        }
        return $aDatos;
    }

    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?CertificadoRecibido
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return CertificadoRecibido::fromArray($aDatos);
    }

    public function getNewId_item()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('e_certificados_dl_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}