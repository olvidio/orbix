<?php

namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\ConverterJson;
use core\DBConnection;
use JsonException;
use stdClass;
use function core\is_true;

class PersonaNotaOtraRegionStgrDB extends PersonaNotaDB
{
    /**
     * Json_certificados de EscritoDB
     *
     * @var string|null
     */
    protected ?string $json_certificados = null;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    public function __construct(string $esquema_region_stgr, ?array $a_id = NULL)
    {
        $db = (ConfigGlobal::mi_sfsv() === 1) ? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        $oConfigDB = new ConfigDB($db); //de la database sv/sf
        $config = $oConfigDB->getEsquema($esquema_region_stgr);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
                if (($nom_id === 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id;
                if (($nom_id === 'id_nivel') && $val_id !== '') $this->iid_nivel = (int)$val_id;
                if (($nom_id === 'tipo_acta') && $val_id !== '') $this->itipo_acta = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_otra_region_stgr');
    }

    public function DBGuardar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = array();
        $aDades['id_nivel'] = $this->iid_nivel;
        $aDades['id_asignatura'] = $this->iid_asignatura;
        $aDades['id_situacion'] = $this->iid_situacion;
        $aDades['acta'] = $this->sacta;
        $aDades['f_acta'] = $this->df_acta;
        $aDades['detalle'] = $this->sdetalle;
        $aDades['preceptor'] = $this->bpreceptor;
        $aDades['id_preceptor'] = $this->iid_preceptor;
        $aDades['epoca'] = $this->iepoca;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['nota_num'] = $this->inota_num;
        $aDades['nota_max'] = $this->inota_max;
        $aDades['tipo_acta'] = $this->itipo_acta;
        $aDades['json_certificados'] = $this->json_certificados;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['preceptor'])) {
            $aDades['preceptor'] = 'true';
        } else {
            $aDades['preceptor'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_nivel	             = :id_nivel,
					id_asignatura            = :id_asignatura,
					id_situacion             = :id_situacion,
					acta                     = :acta,
					f_acta                   = :f_acta,
					detalle                  = :detalle,
					preceptor                = :preceptor,
					id_preceptor             = :id_preceptor,
					epoca                    = :epoca,
					id_activ                 = :id_activ,
					nota_num                 = :nota_num,
					nota_max                 = :nota_max,
					tipo_acta                = :tipo_acta,
                    json_certificados        = :json_certificados";
            $sql_prepare = "UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom AND id_nivel=$this->iid_nivel  AND tipo_acta=$this->itipo_acta ";
            if (($oDblSt = $oDbl->prepare($sql_prepare)) === false) {
                $sClauError = 'PersonaNota.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PersonaNota.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,id_nivel,id_asignatura,id_situacion,acta,f_acta,detalle,preceptor,id_preceptor,epoca,id_activ,nota_num,nota_max,tipo_acta,json_certificados)";
            $valores = "(:id_nom,:id_nivel,:id_asignatura,:id_situacion,:acta,:f_acta,:detalle,:preceptor,:id_preceptor,:epoca,:id_activ,:nota_num,:nota_max,:tipo_acta,:json_certificados)";
            //echo "INSERT INTO $nom_tabla $campos VALUES $valores";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'PersonaNota.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PersonaNota.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
        $this->setAllAtributes($aDades);
        return true;
    }

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes($aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_nivel', $aDades)) $this->setId_nivel($aDades['id_nivel']);
        if (array_key_exists('id_asignatura', $aDades)) $this->setId_asignatura($aDades['id_asignatura']);
        if (array_key_exists('id_situacion', $aDades)) $this->setId_situacion($aDades['id_situacion']);
        // la fecha debe estar antes del acta por si hay que usar la funcion inventarActa.
        if (array_key_exists('f_acta', $aDades)) $this->setF_acta($aDades['f_acta'], $convert);
        if (array_key_exists('acta', $aDades)) $this->setActa($aDades['acta']);
        if (array_key_exists('detalle', $aDades)) $this->setDetalle($aDades['detalle']);
        if (array_key_exists('preceptor', $aDades)) $this->setPreceptor($aDades['preceptor']);
        if (array_key_exists('id_preceptor', $aDades)) $this->setId_preceptor($aDades['id_preceptor']);
        if (array_key_exists('epoca', $aDades)) $this->setEpoca($aDades['epoca']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('nota_num', $aDades)) $this->setNota_num($aDades['nota_num']);
        if (array_key_exists('nota_max', $aDades)) $this->setNota_max($aDades['nota_max']);
        if (array_key_exists('tipo_acta', $aDades)) $this->setTipo_acta($aDades['tipo_acta']);
        if (array_key_exists('json_certificados', $aDades)) $this->setJson_certificados($aDades['json_certificados'], TRUE);
    }

    /**
     *
     * @param boolean $bArray si hay que devolver un array en vez de un objeto.
     * @return array|stdClass|null
     * @throws JsonException
     */
    public function getJson_certificados(bool $bArray = FALSE): array|stdClass|null
    {
        if (!isset($this->json_certificados) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return (new ConverterJson($this->json_certificados, $bArray))->fromPg();
    }

    /**
     * @param string|array|null $oJSON json_certificados
     * @param boolean $db =FALSE optional. Para determinar la variable que se le pasa es ya un objeto json,
     *  o es una variable de php hay que convertirlo. En la base de datos ya es json.
     * @throws JsonException
     */
    public function setJson_certificados(string|array|null $oJSON, bool $db = FALSE): void
    {
        $a_json_certificados = (new ConverterJson($oJSON, FALSE))->toPg($db);
        if ($a_json_certificados === "[]" || empty($a_json_certificados)) {
            $this->json_certificados = null;
        } else {
            $this->json_certificados = (new ConverterJson($oJSON, FALSE))->toPg($db);
        }
    }
}
