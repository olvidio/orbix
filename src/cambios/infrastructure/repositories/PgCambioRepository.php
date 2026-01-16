<?php

namespace src\cambios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\ConverterJson;
use core\Set;
use DateInterval;
use DateTime;
use JsonException;
use PDO;
use src\cambios\domain\contracts\CambioRepositoryInterface;
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
class PgCambioRepository extends ClaseRepository implements CambioRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('av_cambios');
    }

    public function getNomActivEliminada($iId): string
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ=$iId AND id_tipo_cambio=3")) === false) {
            $sClauError = 'ActividadCambio.NomActivEliminada';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
        if ($aDades === FALSE) {
            $nomActiv = _('Actividad Eliminada');
        } else {
            $nomActiv = $aDades['valor_old'] . "(" . _('Eliminado') . ")";
        }
        return $nomActiv;
    }

    /**
     *
     * Cambios: dl y public
     *   Si sólo hago los de dl, al final en public quedarán los de las dl que no tienen el módulo de cambios.
     *   Hay que borrar todo desde public. El primero que borre puede tener los ids para eliminar en las otras tablas,
     *   pero los que lo hagan a continuación no van a saber lo que se ha borrado.
     *
     * cambios_anotados(dl)
     *   Por lo dicho arriba, hay que borrar con un LEFT JOIN con los ids que no existan
     *
     * cambios_usuario(dl)
     *   idem.
     *
     * @param string $str_interval
     */
    public function borrarCambios(string $str_interval = 'P1Y'):void
    {
        $this->borrarCambiosP($str_interval);
        $this->borrarCambiosAnotados();
        $this->borrarCambiosUsuario();
    }

    /**
     * Elimina de la tabla usuario, los registros de cambios que ya se han eliminado
     * @return boolean
     */
    private function borrarCambiosUsuario()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios');

        $sQry = "DELETE FROM av_cambios_usuario USING av_cambios_usuario a
                LEFT JOIN public.av_cambios c
                 ON (a.id_schema_cambio = c.id_schema AND a.id_item_cambio = c.id_item_cambio)
                WHERE av_cambios_usuario.id_item = a.id_item
                AND c.id_item_cambio IS NULL
                ";

        return $this->pdoExec($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Elimina de la tabla anotados, los registros de cambios que ya se han eliminado
     * @return boolean
     */
    private function borrarCambiosAnotados()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios');

        if (getenv('UBICACION') === 'sv') {
            $nom_tabla_anotados = 'av_cambios_anotados_dl';
        } else {
            $nom_tabla_anotados = 'av_cambios_anotados_dl_sf';
        }

        $sQry = "DELETE FROM $nom_tabla_anotados USING $nom_tabla_anotados a
                LEFT JOIN public.av_cambios c
                 ON (a.id_schema_cambio = c.id_schema AND a.id_item_cambio = c.id_item_cambio)
                WHERE $nom_tabla_anotados.id_item = a.id_item
                AND c.id_item_cambio IS NULL
                ";

        return $this->pdoExec($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Elimina els apunts amb un timestamp anterior en un interval
     *
     * @return
     */
    private function borrarCambiosP($str_interval = 'P1Y')
    {
        $oDbl = $GLOBALS['oDBC'];
        $nom_tabla = $this->getNomTabla();

        $interval = new DateInterval($str_interval);
        $oDateTime = new DateTime();
        $timestamp = $oDateTime->sub($interval)->format('Y-m-d 00:00:00');

        $sQry = "DELETE FROM public.$nom_tabla
                WHERE timestamp_cambio < '$timestamp'
                ";

        return $this->pdoExec($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * retorna l'array d'objectes de tipus Cambio
     * Que no s'hagin apuntat a la dl.
     *
     * @return array|false
     */
    public function getCambiosNuevos(): array
    {
        $oDbl = $GLOBALS['oDBC'];

        if (getenv('UBICACION') === 'sv') {
            $nom_tabla_anotados = 'av_cambios_anotados_dl';
        } else {
            $nom_tabla_anotados = 'av_cambios_anotados_dl_sf';
        }

        // Unir los anotados en servidor 1 y en servidor 2:
        //select * from "H-dlb".av_cambios_anotados_dl UNION SELECT * from "H-dlb".av_cambios_anotados_dl_sf

        // Cuando av_cambios_anotados no tiene la fila, No podemos saber si es un cambio de la dl o no.
        // Vamos a hacer dos consultas separadas y unimos.

        $oCambioSet = new Set();
        // Cambios Dl (av_cambios_dl)
        $sQry = "SELECT c.id_schema, c.id_item_cambio, c.id_tipo_cambio, c.id_activ, c.id_tipo_activ, 
                c.json_fases_sv, c.json_fases_sf, c.dl_org,
                c.objeto, c.propiedad, c.valor_old, c.valor_new, c.quien_cambia, c.sfsv_quien_cambia, c.timestamp_cambio
                FROM av_cambios_dl c LEFT JOIN $nom_tabla_anotados a
                ON (c.id_schema = a.id_schema_cambio AND c.id_item_cambio=a.id_item_cambio)
                WHERE a.anotado IS NULL OR a.anotado = 'f'
                ORDER BY dl_org,id_tipo_activ,timestamp_cambio
                ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        foreach ($stmt as $aDatos) {
            $oCambio = Cambio::fromArray($aDatos);
            $oCambioSet->add($oCambio);
        }

        // Cambios NO dl (sólo public.av_cambios)
        $sQry = "SELECT c.id_schema, c.id_item_cambio, c.id_tipo_cambio, c.id_activ, c.id_tipo_activ, 
                c.json_fases_sv, c.json_fases_sf, c.dl_org,
                c.objeto, c.propiedad, c.valor_old, c.valor_new, c.quien_cambia, c.sfsv_quien_cambia, c.timestamp_cambio
                FROM ONLY public.av_cambios c LEFT JOIN $nom_tabla_anotados a
                ON (c.id_schema = a.id_schema_cambio AND c.id_item_cambio=a.id_item_cambio)
                WHERE a.anotado IS NULL OR a.anotado = 'f'
                ORDER BY dl_org,id_tipo_activ,timestamp_cambio
                ";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        foreach ($stmt as $aDatos) {
            $oCambio = Cambio::fromArray($aDatos);
            $oCambioSet->add($oCambio);
        }

        return $oCambioSet->getTot();
    }


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CambioDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CambioDl
     * @throws JsonException
     */
    public function getCambios(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CambioDlSet = new Set();
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
            // para las fechas del postgres (texto iso)
            $aDatos['timestamp_cambio'] = (new ConverterDate('timestamp', $aDatos['timestamp_cambio']))->fromPg();
            // para los json
            $aDatos['json_fases_sv'] = (new ConverterJson($aDatos['json_fases_sv'],true))->fromPg();
            $aDatos['json_fases_sf'] = (new ConverterJson($aDatos['json_fases_sf'], true))->fromPg();
            $CambioDl = Cambio::fromArray($aDatos);
            $CambioDlSet->add($CambioDl);
        }
        return $CambioDlSet->getTot();
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
        $aDatos['json_fases_sv'] = (new ConverterJson($Cambio->getJson_fases_sv(),false))->toPg(false);
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
            /* uso el id_schema 3000, que no debería corresponder a ningun esquema (en todo caso a 'public')
             * Es para el caso de las dl que no tienen instalado el módulo de 'cambios'. Para distinguir los cambios
             * debo usar la dl_org. No uso el id_schema correspondiente, porque si más tarde instalan el módulo
             * 'cambios', puede haber conflico con el id_item_cambio.
             */
            $mi_esquema = 3000;
            $campos = "(id_schema,id_item_cambio,id_tipo_cambio,id_activ,id_tipo_activ,json_fases_sv,json_fases_sf,id_status,dl_org,objeto,propiedad,valor_old,valor_new,quien_cambia,sfsv_quien_cambia,timestamp_cambio)";
            $valores = "($mi_esquema,:id_item_cambio,:id_tipo_cambio,:id_activ,:id_tipo_activ,:json_fases_sv,:json_fases_sf,:id_status,:dl_org,:objeto,:propiedad,:valor_old,:valor_new,:quien_cambia,:sfsv_quien_cambia,:timestamp_cambio)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);    }
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