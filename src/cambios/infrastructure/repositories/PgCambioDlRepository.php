<?php

namespace src\cambios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\ConverterJson;
use core\Set;
use JsonException;
use PDO;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla av_cambios_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
class PgCambioDlRepository extends PgCambioRepository implements CambioDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('av_cambios_dl');
    }


    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cambio $Cambio): bool
    {
        $id_item_cambio = $Cambio->getId_item_cambio();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item_cambio = $id_item_cambio";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     * @throws JsonException
     */
    public function Guardar(Cambio $Cambio): bool
    {
        $id_item_cambio = $Cambio->getId_item_cambio();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item_cambio);

        $aDatos = $Cambio->toArrayForDatabase([
            'timestamp_cambio' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
            'json_fases_sv' => fn($v) => (new ConverterJson($v, false))->toPg(false),
            'json_fases_sf' => fn($v) => (new ConverterJson($v, false))->toPg(false),
        ]);

        /*
        $aDatos = [];
        $aDatos['id_tipo_cambio'] = $Cambio->getTipoCambioVo()->value();
        $aDatos['id_activ'] = $Cambio->getId_activ();
        $aDatos['id_tipo_activ'] = $Cambio->getIdTipoActivVo()->value();
        $aDatos['id_status'] = $Cambio->getId_status();
        $aDatos['dl_org'] = $Cambio->getDl_org();
        $aDatos['objeto'] = $Cambio->getObjeto();
        $aDatos['propiedad'] = $Cambio->getPropiedad();
        $aDatos['valor_old'] = $Cambio->getValor_old();
        $aDatos['valor_new'] = $Cambio->getValor_new();
        $aDatos['quien_cambia'] = $Cambio->getQuien_cambia();
        $aDatos['sfsv_quien_cambia'] = $Cambio->getSfsv_quien_cambia();
        // para las fechas
        $aDatos['timestamp_cambio'] = (new ConverterDate('timestamp', $Cambio->getTimestamp_cambio()))->toPg();
        // para los json
        $aDatos['json_fases_sv'] = (new ConverterJson($Cambio->getJson_fases_sv(), false))->toPg(false);
        $aDatos['json_fases_sf'] = (new ConverterJson($Cambio->getJson_fases_sf(), false))->toPg(false);
        array_walk($aDatos, 'core\poner_null');
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item_cambio']);
            $update = "
					id_tipo_cambio           = :id_tipo_cambio,
					id_activ                 = :id_activ,
					id_tipo_activ            = :id_tipo_activ,
					json_fases_sv            = :json_fases_sv,
					json_fases_sf            = :json_fases_sf,
					id_status                = :id_status,
					dl_org                   = :dl_org,
					objeto                   = :objeto,
					propiedad                = :propiedad,
					valor_old                = :valor_old,
					valor_new                = :valor_new,
					quien_cambia             = :quien_cambia,
					sfsv_quien_cambia        = :sfsv_quien_cambia,
					timestamp_cambio         = :timestamp_cambio";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item_cambio = $id_item_cambio";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item_cambio,id_tipo_cambio,id_activ,id_tipo_activ,json_fases_sv,json_fases_sf,id_status,dl_org,objeto,propiedad,valor_old,valor_new,quien_cambia,sfsv_quien_cambia,timestamp_cambio)";
            $valores = "(:id_item_cambio,:id_tipo_cambio,:id_activ,:id_tipo_activ,:json_fases_sv,:json_fases_sf,:id_status,:dl_org,:objeto,:propiedad,:valor_old,:valor_new,:quien_cambia,:sfsv_quien_cambia,:timestamp_cambio)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item_cambio): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_cambio = $id_item_cambio";
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
     * @param int $id_item_cambio
     * @return array|bool
     * @throws JsonException
     */
    public function datosById(int $id_item_cambio): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_cambio = $id_item_cambio";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['timestamp_cambio'] = (new ConverterDate('timestamp', $aDatos['timestamp_cambio']))->fromPg();
        }
        // para los json
        if ($aDatos !== false) {
            $aDatos['json_fases_sv'] = (new ConverterJson($aDatos['json_fases_sv'],true))->fromPg();
            $aDatos['json_fases_sf'] = (new ConverterJson($aDatos['json_fases_sf'], true))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_item_cambio en la base de datos .
     * @throws JsonException
     */
    public function findById(int $id_item_cambio): ?Cambio
    {
        $aDatos = $this->datosById($id_item_cambio);
        if (empty($aDatos)) {
            return null;
        }
        return Cambio::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('av_cambios_dl_id_item_cambio_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}