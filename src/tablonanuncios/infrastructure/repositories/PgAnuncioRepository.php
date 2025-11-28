<?php

namespace src\tablonanuncios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use PDOException;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\entity\Anuncio;
use src\tablonanuncios\domain\value_objects\AnuncioId;


/**
 * Clase que adapta la tabla e_certificados_rstgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/2/2023
 */
class PgAnuncioRepository extends ClaseRepository implements AnuncioRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('tablon_anuncios');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Certificado
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Certificado
     */
    public function getAnuncios(array $aWhere = [], array $aOperators = []): false|array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $AnuncioSet = new Set();
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
            $sClaveError = 'PgAnuncioRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgAnuncioRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['tanotado'] = (new ConverterDate('timestamp', $aDatos['tanotado']))->fromPg();
            $aDatos['teliminado'] = (new ConverterDate('timestamp', $aDatos['teliminado']))->fromPg();
            $Anuncio = new Anuncio();
            $Anuncio->setAllAttributes($aDatos);
            $AnuncioSet->add($Anuncio);
        }
        return $AnuncioSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Anuncio $Anuncio): bool
    {
        $uuid_item = $Anuncio->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgCertificadoRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Anuncio $Anuncio): bool
    {
        $uuid_item = $Anuncio->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($uuid_item);

        $aDatos = [];
        $aDatos['usuario_creador'] = $Anuncio->getUsuarioCreador();
        $aDatos['esquema_emisor'] = $Anuncio->getEsquemaEmisor();
        $aDatos['esquema_destino'] = $Anuncio->getEsquemaDestino();
        $aDatos['texto_anuncio'] = $Anuncio->getTextoAnuncio();
        $aDatos['idioma'] = $Anuncio->getIdioma();
        $aDatos['tablon'] = $Anuncio->getTablon();
        $aDatos['categoria'] = $Anuncio->getCategoria();

        // para las fechas
        $aDatos['tanotado'] = (new ConverterDate('timestamp', $Anuncio->getTanotado()))->toPg();
        $aDatos['teliminado'] = (new ConverterDate('timestamp', $Anuncio->getTeliminado()))->toPg();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = " usuario_creador  = :usuario_creador,
                    esquema_emisor   = :esquema_emisor,
                    esquema_destino  = :esquema_destino,
                    texto_anuncio    = :texto_anuncio,
                    idioma           = :idioma,
                    tablon           = :tablon,
                    tanotado        = :tanotado,
                    teliminado      = :teliminado,
                    categoria        = :categoria";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE uuid_item = '$uuid_item'")) === FALSE) {
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
            $aDatos['uuid_item'] = $Anuncio->getUuid_item();
            $campos = "(uuid_item,usuario_creador,esquema_emisor,esquema_destino,texto_anuncio,idioma,tablon,tanotado,teliminado,categoria)";
            $valores = "(:uuid_item,:usuario_creador,:esquema_emisor,:esquema_destino,:texto_anuncio,:idioma,:tablon,:tanotado,:teliminado,:categoria)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgAnuncioRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgAnuncioRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(AnuncioId $uuid_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
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
     * @param AnuncioId $uuid_item
     * @return array|bool
     */
    public function datosById(AnuncioId $uuid_item): bool|array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgCertificadoRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== FALSE) {
            $aDatos['tanotado'] = (new ConverterDate('timestamp', $aDatos['tanotado']))->fromPg();
            $aDatos['teliminado'] = (new ConverterDate('timestamp', $aDatos['teliminado']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(AnuncioId $uuid_item): ?Anuncio
    {
        $aDatos = $this->datosById($uuid_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new Anuncio())->setAllAttributes($aDatos);
    }

}