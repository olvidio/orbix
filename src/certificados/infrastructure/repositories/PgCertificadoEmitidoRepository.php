<?php

namespace src\certificados\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use PDOException;
use RuntimeException;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use function core\is_true;


/**
 * Clase que adapta la tabla e_certificados_rstgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
class PgCertificadoEmitidoRepository extends ClaseRepository implements CertificadoEmitidoRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_certificados_rstgr');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Certificado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Certificado
     */
    public function getCertificados(array $aWhere = [], array $aOperators = []): bool|array
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
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClaveError = 'PgCertificadoRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        } else {
            try {
                $oDblSt->execute($aWhere);
            } catch (\PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgCertificadoRepository.listar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
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
            $aDatos['f_enviado'] = (new ConverterDate('date', $aDatos['f_enviado']))->fromPg();
            $Certificado = new CertificadoEmitido();
            $Certificado->setAllAttributes($aDatos);
            $CertificadoSet->add($Certificado);
        }
        return $CertificadoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CertificadoEmitido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item = $id_item")) === FALSE) {
            $sClaveError = 'PgCertificadoRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CertificadoEmitido $Certificado): bool
    {
        $id_item = $Certificado->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = [];
        $aDatos['id_nom'] = $Certificado->getId_nom();
        $aDatos['nom'] = $Certificado->getNom();
        $aDatos['idioma'] = $Certificado->getIdioma();
        $aDatos['destino'] = $Certificado->getDestino();
        $aDatos['certificado'] = $Certificado->getCertificado();
        $aDatos['esquema_emisor'] = $Certificado->getEsquema_emisor();
        $aDatos['firmado'] = $Certificado->isFirmado();
        // para los bytea
        $aDatos['documento'] = bin2hex($Certificado->getDocumento() ?? '');
        // para las fechas
        $aDatos['f_certificado'] = (new ConverterDate('date', $Certificado->getF_certificado()))->toPg();
        $aDatos['f_enviado'] = (new ConverterDate('date', $Certificado->getF_enviado()))->toPg();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['firmado'])) {
            $aDatos['firmado'] = 'true';
        } else {
            $aDatos['firmado'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
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
                    f_enviado                = :f_enviado";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item = $id_item")) === FALSE) {
                $sClaveError = 'PgCertificadoRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgCertificadoRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            // INSERT
            $aDatos['id_item'] = $Certificado->getId_item();
            $campos = "(id_item,id_nom,nom,idioma,destino,certificado,f_certificado,esquema_emisor,firmado,documento,f_enviado)";
            $valores = "(:id_item,:id_nom,:nom,:idioma,:destino,:certificado,:f_certificado,:esquema_emisor,:firmado,:documento,:f_enviado)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgCertificadoRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgCertificadoRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item = $id_item")) === FALSE) {
            $sClaveError = 'PgCertificadoRepository.isNew';
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
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): bool|array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item = $id_item")) === FALSE) {
            $sClaveError = 'PgCertificadoRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        // para los bytea, sobre escribo los valores:
        $sdocumento = '';
        $oDblSt->bindColumn('documento', $sdocumento, PDO::PARAM_STR);
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        if ($aDatos !== FALSE) {
            $aDatos['documento'] = hex2bin($sdocumento ?? '');
        }
        // para las fechas del postgres (texto iso)
        if ($aDatos !== FALSE) {
            $aDatos['f_certificado'] = (new ConverterDate('date', $aDatos['f_certificado']))->fromPg();
            $aDatos['f_enviado'] = (new ConverterDate('date', $aDatos['f_enviado']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): CertificadoEmitido
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            $txt_err = sprintf(_("No se ha encontrado el item %s en la base de datos"), $id_item);
            throw new RuntimeException($txt_err);
        }
        return (new CertificadoEmitido())->setAllAttributes($aDatos);
    }

    public function getNewId_item()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('e_certificados_rstgr_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}