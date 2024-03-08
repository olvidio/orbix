<?php

namespace devel\model;

use actividadcargos\model\entity\Cargo;
use actividadcargos\model\entity\GestorCargo;
use core\ConfigDB;
use core\DBConnection;

class DBAlterSchema
{
    /**
     * oDbl
     *
     * @var object
     */
    protected $oDbl;
    /**
     *
     * @var string
     */
    protected $schema;

    /**
     *
     * @var string
     */
    protected $schema_del;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     */
    function __construct()
    {
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setDbConexion($oDbl)
    {
        $this->setoDbl($oDbl);
    }

    /**
     * Recupera el atributo oDbl de DBAlterSchema
     *
     * @return object oDbl
     */
    protected function setoDbl($oDbl)
    {
        $this->oDbl = $oDbl;
    }

    /**
     * Recupera el atributo oDbl de DBAlterSchema
     *
     * @return object oDbl
     */
    protected function getoDbl()
    {
        return $this->oDbl;
    }

    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    public function setSchemaDel($schema)
    {
        $this->schema_del = $schema;
    }


    /**
     *
     * @param array $aDefaults
     *       ['tabla' => '.u_dir_cdc_dl', 'campos' => '...'],
     */
    public function setInserts($aInserts)
    {
        foreach ($aInserts as $cambio) {
            $tabla = $cambio['tabla'];
            $campos = $cambio['campos'];

            $full_name = "\"$this->schema\".$tabla";
            $full_name_del = "\"$this->schema_del\".$tabla";
            if ($this->existeTabla($full_name_del) && $this->existeTabla($full_name)) {
                $this->executeInsert($tabla, $campos);
            }
        }

    }

    private function executeInsert($tabla, $campos)
    {
        $oDbl = $this->getoDbl();
        $full_name = "\"$this->schema\".$tabla";
        $full_name_del = "\"$this->schema_del\".$tabla";

        $sql = "INSERT INTO $full_name ($campos) SELECT $campos FROM $full_name_del";
        switch ($tabla) {
            case "a_importadas":
                $sql = "INSERT INTO $full_name ($campos) SELECT $campos FROM $full_name_del ON CONFLICT (id_activ) DO NOTHING";
                break;
            case "e_actas_dl":
                $sql = "INSERT INTO $full_name ($campos) SELECT $campos FROM $full_name_del ON CONFLICT (acta) DO NOTHING";
                break;
            case "d_dossiers_abiertos":
                $sql = "INSERT INTO $full_name ($campos) SELECT $campos FROM $full_name_del ON CONFLICT ON CONSTRAINT d_dossiers_abiertos_pkey DO NOTHING";
                break;
            case "p_agregados":
            case "p_de_paso_out":
            case "p_numerarios":
            case "p_supernumerarios":
            case "p_sssc":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS p ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT (id_nom) DO UPDATE
                        SET $update
                        WHERE p.situacion = 'A' AND p.f_situacion > EXCLUDED.f_situacion";
                break;
            case "d_asignaturas_activ_dl":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT ON CONSTRAINT d_asignaturas_activ_dl_pkey DO UPDATE
                        SET $update
                        WHERE a.tipo = 'p' AND EXCLUDED.tipo != 'p' ";
                break;
            case "d_asistentes_dl":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT ON CONSTRAINT d_asistentes_pkey DO UPDATE
                        SET $update
                        WHERE a.id_tabla = 'out' AND EXCLUDED.id_tabla = 'dl' ";
                break;
            case "d_asistentes_out":
                $sql = "INSERT INTO $full_name ($campos) SELECT $campos FROM $full_name_del ON CONFLICT ON CONSTRAINT d_asistentes_out_id_activ_id_nom_pk DO NOTHING";
                break;
            case "d_cargos_activ_dl":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT ON CONSTRAINT d_cargos_activ_dl_id_activ_id_cargo_pkey  DO NOTHING";
                break;
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
     *
     * @param array $aDefaults
     *       ['tabla' => 'a_actividad_proceso_sf', 'campo' => 'id_schema', 'valor' => "idschema('H-dlx'::text)"],
     */
    public function setDefaults($aDefaults)
    {
        foreach ($aDefaults as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $valor = $cambio['valor'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $this->setColumnDefault($full_name, $campo, $valor);
            }
        }

    }

    public function setColumnDefault($nom_tabla, $nom_column, $default)
    {
        $oDbl = $this->getoDbl();
        $sql = "ALTER TABLE $nom_tabla ALTER COLUMN $nom_column SET DEFAULT $default";

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
    }

    /**
     *
     * @param array $aDatos
     *   ['tabla' => 'u_centros_dl', 'campo' => 'id_zona'],
     */
    public function setNullDatos($aDatos)
    {
        $oDbl = $this->getoDbl();
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $sql = "UPDATE $full_name SET $campo = null; ";

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
            }
        }
    }


    /**
     *
     * @param array $aDatos
     *      ['tabla' => 'da_plazas_dl', 'campo' => 'id_dl', 'old' => "$id_dl_old", 'new' => "$id_dl_new"],
     */
    public function updateDatos($aDatos)
    {
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $old = $cambio['old'];
            $new = $cambio['new'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $this->updateColumn($full_name, $campo, $old, $new);
            }
        }
    }

    public function updateColumn($full_name, $campo, $old, $new)
    {
        $oDbl = $this->getoDbl();
        $sql = "UPDATE $full_name SET $campo = '$new' WHERE $campo = '$old' ";

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
    }

    /**
     *
     * @param array $aDatos
     *     ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "$dl_old(.*)", 'replacement' => "$DlNew\1"]
     */
    public function updateDatosRegexp($aDatos)
    {
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $pattern = $cambio['pattern'];
            $replacement = $cambio['replacement'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $this->updateColumnRegexp($full_name, $campo, $pattern, $replacement);
            }
        }
    }

    public function updateColumnRegexp($full_name, $campo, $pattern, $replacement)
    {
        $oDbl = $this->getoDbl();
        $sql = "UPDATE $full_name SET $campo = regexp_replace($campo, '$pattern', '$replacement' ,'g') ";

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
    }

    public function updateCedidasAll($old, $new)
    {
        $oDbl = $this->getoDbl();
        $sql = "UPDATE publicv.da_plazas set cedidas =  regexp_replace(cedidas::text, '\m$old\M', '$new', 'g')::jsonb where cedidas is not null;";

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
    }

    public function updatePropietarioAll($old, $new)
    {
        // conectar con DB sv-e:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('publicv-e'); //de la database comun
        $oConexion = new DBConnection($config);
        $oDbSve = $oConexion->getPDO();

        $sql = "UPDATE global.d_asistentes_dl set propietario = regexp_replace(propietario::text, '\m$old\M', '$new', 'g')::text where propietario is not null;";

        if (($oDblSt = $oDbSve->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbSve, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
        $sql = "UPDATE publicv.d_asistentes_de_paso set propietario = regexp_replace(propietario::text, '\m$old\M', '$new', 'g')::text where propietario is not null;";

        if (($oDblSt = $oDbSve->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbSve, $sClauError, __LINE__, __FILE__);
            return false;
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    /**
     *
     * @param array $aDatos
     *     ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "$dl_old(.*)", 'replacement' => "$DlNew\1"]
     */
    public function updateDatosRegexpTodos($aDatos)
    {
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $pattern = $cambio['pattern'];
            $replacement = $cambio['replacement'];

            if ($this->existeTabla($tabla)) {
                $this->updateColumnRegexp($tabla, $campo, $pattern, $replacement);
            }
        }
    }

    /**
     * Para el esquema_del,
     *  pasa las asistencias_out a asistencias_dl para el caso de asistencias a actividades que organiza la dl_new.
     *
     * @param string $dl_new (dl_org)
     */
    public function asistentesOut2Dl($dl_new)
    {
        // conectar con DB comun:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('public'); //de la database comun

        $oConexion = new DBConnection($config);
        $oDbComun = $oConexion->getPDO();
        // Conexión actual (a sv-e).
        $oDbl = $this->getoDbl();
        // buscar actividades de asistencias out que tengan dl_org = dl_new
        $full_asistentes_out = "\"$this->schema_del\".d_asistentes_out";
        $full_asistentes_dl = "\"$this->schema_del\".d_asistentes_dl";

        // sv-e
        $sQuery = "SELECT DISTINCT id_activ FROM $full_asistentes_out";

        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'AlterSchemna.asistenteOut';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aId_activ = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_activ = $aDades['id_activ'];
            // buscar la dl_org
            $sql = "SELECT dl_org FROM a_actividades_all WHERE id_activ = $id_activ ";
            $dl_org = $oDbComun->query($sql)->fetchColumn();
            if ($dl_org == $dl_new) {
                $aId_activ[] = $id_activ;
            }
        }

        if (!empty($aId_activ)) {
            // mover las asistencias_out de $aId_activ a asistencias_dl
            $txt_ids = implode(',', $aId_activ);
            $condicion = "id_activ IN ($txt_ids)";
            $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';

            $insert = "INSERT INTO $full_asistentes_dl ($campos) 
                        SELECT $campos FROM $full_asistentes_out WHERE $condicion
                        ON CONFLICT ON CONSTRAINT d_asistentes_pkey DO NOTHING
                    ";
            if (($oDblSt = $oDbl->query($insert)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            // las borro de d_asistencias_out
            $delete = "DELETE FROM $full_asistentes_out WHERE $condicion";
            if (($oDblSt = $oDbl->query($delete)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    /**
     * Para el esquema matriz, pasa las asistencias_out a asistencias_dl para el caso de asistencias
     *  a actividades que están en la dl (las que se han insertado de dl_old) y se borran del
     *  la tabla de a_importadas.
     *
     */
    public function asistentesOut2DlPropia()
    {
        // conectar con DB comun:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('public'); //de la database comun

        $oConexion = new DBConnection($config);
        $oDbComun = $oConexion->getPDO();
        // Conexión actual (a sv-e).
        $oDbl = $this->getoDbl();
        // buscar actividades de asistencias out que esten en a_actividades_dl
        $full_asistentes_out = "\"$this->schema\".d_asistentes_out";
        $full_asistentes_dl = "\"$this->schema\".d_asistentes_dl";

        // sv-e
        $sQuery = "SELECT DISTINCT id_activ FROM $full_asistentes_out";

        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'AlterSchemna.asistenteOut';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $esquema_comun = substr($this->schema, 0, -1); // quito la v o la f.
        $full_actividades_dl = "\"$esquema_comun\".a_actividades_dl";
        $aId_activ = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_activ = $aDades['id_activ'];
            // buscar si están en la dl
            $sql = "SELECT id_activ FROM $full_actividades_dl WHERE id_activ = $id_activ ";
            $id_activ_dl = $oDbComun->query($sql)->fetchColumn();
            if (!empty($id_activ_dl)) {
                $aId_activ[] = $id_activ_dl;
            }
        }

        if (!empty($aId_activ)) {
            // mover las asistencias_out de $aId_activ a asistencias_dl
            $txt_ids = implode(',', $aId_activ);
            $condicion = "id_activ IN ($txt_ids)";
            $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';

            $insert = "INSERT INTO $full_asistentes_dl ($campos) SELECT $campos FROM $full_asistentes_out WHERE $condicion";
            if (($oDblSt = $oDbl->query($insert)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            // borrar de asistentes_out
            $delete = "DELETE FROM $full_asistentes_dl WHERE $condicion";
            if (($oDblSt = $oDbl->query($delete)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            // borrar de a_importadas
            $full_importadas = "\"$esquema_comun\".a_importadas";
            $delete = "DELETE FROM $full_importadas WHERE $condicion";
            if (($oDblSt = $oDbComun->query($delete)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
        }
    }

    /**
     * Ya se han insertado todas las importadas de dl_del en dl_matriz
     * Es para borrar si queda alguna de la propia dl.
     *
     */
    public function comprobarImportadas()
    {
        // conectar con DB comun:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('public'); //de la database comun

        $oConexion = new DBConnection($config);
        $oDbComun = $oConexion->getPDO();

        $esquema_comun = substr($this->schema, 0, -1); // quito la v o la f.
        $full_actividades_dl = "\"$esquema_comun\".a_actividades_dl";
        $full_actividades_importadas = "\"$esquema_comun\".a_importadas";

        $sql = "DELETE FROM $full_actividades_importadas WHERE id_activ IN (SELECT id_activ FROM $full_actividades_dl ) ";
        $oDbComun->query($sql);
    }


    /**
     * Es necesario una función extra para intentar añadir los cargos duplicados
     * por ejemplo los sacd: puede haber dos sacd en la actividad, uno en cada dl con
     * id_cargo=35. Se añade el segundo con id_cargo=36.
     */
    public function insertarCargos()
    {
        $oDbl = $this->getoDbl();
        $tabla = 'd_cargos_activ_dl';
        $campos = 'id_activ, id_cargo, id_nom, puede_agd, observ';

        $full_name = "\"$this->schema\".$tabla";
        $full_name_del = "\"$this->schema_del\".$tabla";

        // buscar cargos repetidos:
        $sql = "SELECT d.id_activ, d.id_cargo, m.id_nom as id_nom_matriz, d.id_nom as id_nom_del
                FROM $full_name m JOIN $full_name_del d ON (m.id_activ=d.id_activ AND m.id_cargo=d.id_cargo)";
        if ($oDbl->query($sql) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.execute';
            //$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        // tipos de cargo:
        $gesCargos = new GestorCargo();
        foreach ($oDbl->query($sql) as $aDades) {
            $id_activ = $aDades['id_activ'];
            $id_cargo = $aDades['id_cargo'];
            // comprobar que no sea el mismo id_nom...
            $id_nom_matriz = $aDades['id_nom_matriz'];
            $id_nom_del = $aDades['id_nom_del'];
            if ($id_nom_matriz == $id_nom_del) {
                continue;
            }
            $oCargo = new Cargo($id_cargo);
            $tipo_cargo = $oCargo->getTipo_cargo();
            $cargos_de_tipo = $gesCargos->getArrayCargosDeTipo($tipo_cargo);
            $txt_ids = implode(',', array_keys($cargos_de_tipo));
            $condicion_cargo = " AND id_cargo IN ($txt_ids)";
            // buscar el id_cargo mayor (del mismo tipo) para la actividad:
            $sql1 = "SELECT id_cargo FROM $full_name WHERE id_activ = $id_activ $condicion_cargo ";
            $sql2 = "SELECT id_cargo FROM $full_name WHERE id_activ = $id_activ $condicion_cargo ";
            $sql = "$sql1 UNION $sql2 ORDER BY 1 DESC";
            $id_cargo_max = $oDbl->query($sql)->fetchColumn();
            $id_cargo_max++;
            // compruebo que está en el rango del tipo cargo, sino lo desprecio.
            if (!empty($cargos_de_tipo[$id_cargo_max])) {
                $update = "UPDATE $full_name_del SET id_cargo = $id_cargo_max WHERE id_activ= $id_activ AND id_cargo = $id_cargo";
                $oDbl->query($sql);
            }
        }

        // Ahora si insertar:
        $a_campos = explode(',', $campos);
        $update = '';
        foreach ($a_campos as $campo) {
            $campo = trim($campo);
            $update .= empty($update) ? '' : ', ';
            $update .= "$campo = EXCLUDED.$campo";
        }
        $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                    ON CONFLICT ON CONSTRAINT d_cargos_activ_dl_id_activ_id_cargo_pkey DO NOTHING";

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
    }

    /**
     * Quita las herencias de todas para el esquema $esquema de la conexion (DB)
     *
     * @param \PDO $conexionDB
     * @param string $esquema
     * @return boolean
     */
    public function quitarHerencias($conexionDB, $esquema)
    {

        $sql = "SELECT c.relname AS child, p.relname AS parent, n1.nspname as schema_parent, n.nspname as schema_child 
                FROM pg_inherits JOIN pg_class AS c ON (inhrelid=c.oid) 
                    JOIN pg_class as p ON (inhparent=p.oid), pg_namespace n, pg_namespace n1 
                WHERE c.relnamespace = n.oid AND n.nspname='$esquema' AND p.relnamespace = n1.oid;";

        if (($conexionDB->query($sql)) === false) {
            $sClauError = 'AlterSchemna.asistenteOut';
            $_SESSION['oGestorErrores']->addErrorAppLastError($conexionDB, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($conexionDB->query($sql) as $aDades) {
            $child = $aDades['child'];
            $schema_child = $aDades['schema_child'];
            $parent = $aDades['parent'];
            $schema_parent = $aDades['schema_parent'];

            $full_child = "\"$schema_child\".$child";
            $full_parent = "\"$schema_parent\".$parent";

            $sQuery = "ALTER TABLE $full_child NO INHERIT $full_parent ";
            $conexionDB->query($sQuery);
        }
    }


}
