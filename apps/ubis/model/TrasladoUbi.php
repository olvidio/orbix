<?php

namespace ubis\model;

use core;

class TrasladoUbi
{
    /**
     * oDbl
     *
     * @var object
     */
    private $oDbl;
    /**
     *
     * @var string
     */
    private $esquema_dst;
    /**
     *
     * @var string
     */
    private $esquema_org;
    /**
     *
     * @var integer
     */
    private $id_ubi;


    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     */
    function __construct()
    {
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/


    public function trasladoCdc($id_ubi)
    {
        $this->setId_ubi($id_ubi);

        $oConfigDB = new core\ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('public'); //de la database comun

        $oConexion = new core\DBConnection($config);
        $oDbl = $oConexion->getPDO();
        $this->setODbl($oDbl);

        // tablas:
        $aInserts = [];

        $tabla = 'du_gastos_dl'; // importa el orden: después de u_cdc_dl(id_ubi)
        $campos = 'id_ubi, f_gasto, tipo, cantidad';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'du_grupos_dl'; // importa el orden: después de u_cdc_dl(id_ubi)
        $campos = 'id_ubi_padre, id_ubi_hijo';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'du_periodos';
        $campos = 'id_ubi, f_ini, f_fin, sfsv';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'd_teleco_cdc_dl';
        $campos = 'id_ubi, tipo_teleco, desc_teleco, num_teleco, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        foreach ($aInserts as $cambio) {
            $tabla = $cambio['tabla'];
            $campos = $cambio['campos'];

            $this->executeInsert($tabla, $campos, '');
        }
        // direcciones
        $this->executeDirecciones('');
    }

    public function trasladoCtr($id_ubi)
    {
        $this->setId_ubi($id_ubi);

        $oConfigDB = new core\ConfigDB('importar'); //de la database sv
        $config = $oConfigDB->getEsquema('publicv'); //de la database sv

        $oConexion = new core\DBConnection($config);
        $oDbl = $oConexion->getPDO();
        $this->setODbl($oDbl);

        // tablas:
        $aInserts = [];

        $tabla = 'd_teleco_ctr_dl';
        $campos = 'id_ubi, tipo_teleco, desc_teleco, num_teleco, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];
        $tabla = 'du_presentacion_dl';
        $campos = 'id_direccion, id_ubi, pres_nom, pres_telf, pres_mail, zona, observ';
        $aInserts[] = ['tabla' => $tabla, 'campos' => $campos];

        foreach ($aInserts as $cambio) {
            $tabla = $cambio['tabla'];
            $campos = $cambio['campos'];

            $this->executeInsert($tabla, $campos, 'v');
        }
        // direcciones
        $this->executeDirecciones('v');

    }

    private function executeDirecciones($sv)
    {
        $oDbl = $this->getoDbl();
        $id_ubi = $this->getId_ubi();

        if ($sv == 'v') {
            $tabla_ubi = 'u_centros_dl';
            $tabla_dir = 'u_dir_ctr_dl';
            $tabla_cross = 'u_cross_ctr_dl_dir';
            $constrain = 'u_cross_ctr_dl_dir_pkey';
            $campos_ubi = 'tipo_ubi, id_ubi, nombre_ubi, pais, status, f_status, sv, sf, tipo_ctr, tipo_labor, cdc, id_ctr_padre, n_buzon, num_pi, num_cartas, observ, num_habit_indiv, plazas, sede, num_cartas_mensuales';
        } else {
            $tabla_ubi = 'u_cdc_dl';
            $tabla_dir = 'u_dir_cdc_dl';
            $tabla_cross = 'u_cross_cdc_dl_dir';
            $constrain = 'u_cross_cdc_dl_dir_pkey';
            $campos_ubi = 'tipo_ubi, id_ubi, nombre_ubi, pais, status, f_status, sv, sf, tipo_casa, plazas, plazas_min, num_sacd, biblioteca, observ';
        }

        $full_name_ubi_org = "\"$this->esquema_org$sv\".$tabla_ubi";
        $full_name_ubi_dst = "\"$this->esquema_dst$sv\".$tabla_ubi";
        $full_name_org = "\"$this->esquema_org$sv\".$tabla_dir";
        $full_name_dst = "\"$this->esquema_dst$sv\".$tabla_dir";
        $full_name_cross_org = "\"$this->esquema_org$sv\".$tabla_cross";
        $full_name_cross_dst = "\"$this->esquema_dst$sv\".$tabla_cross";

        // Ojo con el centro/casa, como esta relacionado en cross, no se puede borrar hasta el final.
        $insert_ubi = "INSERT INTO $full_name_ubi_dst ($campos_ubi) 
                SELECT $campos_ubi FROM $full_name_ubi_org WHERE id_ubi = $id_ubi
                ON CONFLICT (id_ubi) DO NOTHING";
        $oDbl->query($insert_ubi);

        $sql = "SELECT id_direccion FROM $full_name_cross_org WHERE id_ubi = $id_ubi";

        foreach ($oDbl->query($sql) as $row) {
            $id_direccion = $row[0];
            $campos = 'id_direccion, direccion, c_p, poblacion, provincia, a_p, pais, f_direccion, observ, cp_dcha, latitud, longitud, plano_doc, plano_extension, plano_nom, nom_sede';
            $insert = "INSERT INTO $full_name_dst ($campos) 
                    (SELECT $campos FROM $full_name_org d WHERE id_direccion = $id_direccion)
                    ON CONFLICT (id_direccion) DO NOTHING";
            $oDbl->query($insert);

            // el cross
            $campos = 'id_ubi, id_direccion, propietario, principal';
            $sql2 = "INSERT INTO $full_name_cross_dst ($campos)
                        SELECT $campos FROM $full_name_cross_org WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion
                        ON CONFLICT ON CONSTRAINT $constrain DO NOTHING";
            $oDbl->query($sql2);

            // borrar el origen:
            $sql_del = "DELETE FROM $full_name_org
                            WHERE id_direccion = $id_direccion";
            $oDbl->query($sql_del);
        }


        // borrar el origen:
        $sql_del = "DELETE FROM $full_name_ubi_org WHERE id_ubi = $id_ubi";
        $oDbl->query($sql_del);
    }

    private function executeInsert($tabla, $campos, $sv)
    {
        $oDbl = $this->getoDbl();
        $id_ubi = $this->getId_ubi();

        $full_name_org = "\"$this->esquema_org$sv\".$tabla";
        $full_name_dst = "\"$this->esquema_dst$sv\".$tabla";

        if (!$this->existeTabla($full_name_org) || !$this->existeTabla($full_name_dst)) {
            return FALSE;
        }

        switch ($tabla) {
            case 'd_teleco_ctr_dl':
                $sql = "INSERT INTO $full_name_dst ($campos) 
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi";
                // borrar el origen:
                $sql_del = "DELETE FROM $full_name_org 
                        WHERE id_ubi = $id_ubi";
                break;
            case 'd_teleco_cdc_dl':
                $sql = "INSERT INTO $full_name_dst ($campos) 
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi";
                // borrar el origen:
                $sql_del = "DELETE FROM $full_name_org 
                        WHERE id_ubi = $id_ubi";
                break;
            case 'du_presentacion_dl':
                $sql = "INSERT INTO $full_name_dst ($campos) 
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi
                        ON CONFLICT ON CONSTRAINT du_presentacion_dl_pkey DO NOTHING";
                // borrar el origen:
                $sql_del = "DELETE FROM $full_name_org 
                        WHERE id_ubi = $id_ubi";
                break;
            case 'du_periodos':
                $sql = "INSERT INTO $full_name_dst ($campos) 
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi
                        ON CONFLICT (id_ubi, f_ini) DO NOTHING";
                // borrar el origen:
                $sql_del = "DELETE FROM $full_name_org 
                        WHERE id_ubi = $id_ubi";
                break;
            default:
                $sql = "INSERT INTO $full_name_dst ($campos) 
                        SELECT $campos FROM $full_name_org WHERE id_ubi = $id_ubi
                        ON CONFLICT (id_ubi) DO NOTHING";
                // borrar el origen:
                $sql_del = "DELETE FROM $full_name_org 
                        WHERE id_ubi = $id_ubi";

        }

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
        // borrar el origen:
        if (!empty($sql_del)) {
            if (($oDblSt = $oDbl->prepare($sql_del)) === false) {
                $sClauError = 'DBAlterSchema.crearSchema.prepare';
                $sClauError .= ' ' . $sql_del;
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                if ($oDblSt->execute() === false) {
                    $sClauError = 'DBAlterSchema.crearSchema.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
    }

    /**
     * Comprobar si existe la tabla, para evitar errores.
     *
     * @param string $tabla
     * @return boolean
     */
    public function existeTabla($full_name)
    {

        $oDbl = $this->getoDbl();
        $sql = "SELECT to_regclass('$full_name'); ";

        foreach ($oDbl->query($sql) as $row) {
            if (!empty($row[0])) {
                return TRUE;
            }
        }
        return FALSE;
    }


    /**
     * @return string
     */
    public function getEsquema_dst()
    {
        return $this->esquema_dst;
    }

    /**
     * @param string $esquema_dst
     */
    public function setEsquema_dst($esquema_dst)
    {
        $this->esquema_dst = $esquema_dst;
    }

    /**
     * @return string
     */
    public function getEsquema_org()
    {
        return $this->esquema_org;
    }

    /**
     * @param string $esquema_org
     */
    public function setEsquema_org($esquema_org)
    {
        $this->esquema_org = $esquema_org;
    }

    /**
     * @return number
     */
    public function getId_ubi()
    {
        return $this->id_ubi;
    }

    /**
     * @param number $id_ubi
     */
    public function setId_ubi($id_ubi)
    {
        $this->id_ubi = $id_ubi;
    }

    /**
     * @return object
     */
    public function getODbl()
    {
        return $this->oDbl;
    }

    /**
     * @param object $oDbl
     */
    public function setODbl($oDbl)
    {
        $this->oDbl = $oDbl;
    }


}
