<?php

namespace core;

use ubis\model\entity\GestorDelegacion;

class DBView
{
    /**
     * oDbl de Grupo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * /**
     * Schema de la region stgr
     *
     * @var string
     */
    protected $sSchema;
    /**
     * RegionStgr de DBRol
     *
     * @var string
     */
    protected $sRegionStgr;
    /**
     * Nombre de la tabla para crear la vista
     *
     * @var string
     */
    protected $sView;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     */
    function __construct($schema, $mi_sfsv, $db)
    {
        // Necesito permisos de superusuario para poder acceder a los distintos esquemas
        // que pertenecen a la region del stgr.

        $oConfigDB = new ConfigDB('importar');
        switch ($db) {
            case 'interior':
                if ($mi_sfsv === 1) {
                    $config = $oConfigDB->getEsquema('publicv');
                } elseif ($mi_sfsv === 2) {
                    $config = $oConfigDB->getEsquema('publicf');
                }
                break;
            case 'exterior':
                if ($mi_sfsv === 1) {
                    $config = $oConfigDB->getEsquema('publicv-e');
                } elseif ($mi_sfsv === 2) {
                    $config = $oConfigDB->getEsquema('publicf');
                }
                break;
            case 'comun':
                $config = $oConfigDB->getEsquema('public');
                break;
        }
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        $this->setoDbl($oDbl);
        $this->setSchema($schema);

        // region stgr:
        $a_reg = explode('-', $schema);
        $reg = $a_reg[0];
        $this->setRegionStgr($reg);
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setDbConexion($oDbl)
    {
        $this->setoDbl($oDbl);
    }

    public function setSchema($schema)
    {
        $this->sSchema = $schema;
    }

    public function setRegionStgr($regionStgr)
    {
        $this->sRegionStgr = $regionStgr;
    }

    public function setView($view)
    {
        $this->sView = $view;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    protected function setoDbl($oDbl)
    {
        $this->oDbl = $oDbl;
    }

    /**
     * Recupera el atributo oDbl de Grupo
     *
     * @return object oDbl
     */
    protected function getoDbl()
    {
        return $this->oDbl;
    }


    public function normalizarTexto($str)
    {
        $str = trim($str);
        // saltos de linea y tabuladores
        $search = ["\n", "\t", "\r"];
        $replace = ' ';
        $new_str = str_replace($search, $replace, $str);
        // espacios extra:
        $new_str = preg_replace('/\s\s+/', ' ', $new_str);
        $lower = strtolower($new_str);

        $string = preg_replace('/[^[:print:]]/', '', $lower);

        return $string;
    }

    public function ExisteYEsIgual($comun = FALSE)
    {
        // definición teórica
        $defNew = $this->getDefView($this->sView, $comun);
        // quitar espacios, tabuladores, returns...
        $defNew = $this->normalizarTexto($defNew);
        // definición real
        $defActual = $this->getSqlView($this->sView);
        $defActual = $this->normalizarTexto($defActual);

        if ($defActual == $defNew) {
            return TRUE;
        } else {
            if (!empty($defActual)) { // SI existe, pero es distinta: la borro
                $this->Drop();
            }
            return FALSE;
        }
    }

    public function Create($comun = FALSE)
    {
        /*
         * OJO, hay que dar permisos...
         */
        $oDbl = $this->getoDbl();
        $nameView = " \"$this->sSchema\".$this->sView";

        $sql = "CREATE MATERIALIZED VIEW $nameView AS ";
        $sql .= $this->getDefView($this->sView, $comun);

        if (($oDbl->exec($sql)) === false) {
            $sClauError = 'Refresh';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        } else {
            $this->setOwnerToSchema($nameView);
            return TRUE;
        }

    }

    public function Drop()
    {
        $oDbl = $this->getoDbl();
        $nameView = " \"$this->sSchema\".$this->sView";

        // Sólo puede el propietario. Por eso hay que emplear la conexión oDB
        $this->setOwnerToSuperuser($nameView);
        $sql = "DROP MATERIALIZED VIEW IF EXISTS $nameView CASCADE ";

        if (($oDbl->exec($sql)) === false) {
            $sClauError = 'Drop';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        } else {
            return TRUE;
        }

    }

    public function Refresh()
    {
        $oDbl = $this->getoDbl();
        $nameView = " \"$this->sSchema\".$this->sView";

        // Sólo puede el propietario.
        $this->setOwnerToSuperuser($nameView);

        $sql = "REFRESH MATERIALIZED VIEW $nameView";
        if (($oDbl->exec($sql)) === false) {
            $sClauError = 'Refresh';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        } else {
            $this->setOwnerToSchema($nameView);
            return TRUE;
        }
    }

    private function setOwnerToSuperuser($nameView)
    {
        $oDbl = $this->getoDbl();
        $sql = "ALTER MATERIALIZED VIEW $nameView OWNER TO \"orbix_admindb\"";
        if (($oDbl->exec($sql)) === false) {
            $sClauError = 'Refresh';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
    }

    private function setOwnerToSchema($nameView)
    {
        $oDbl = $this->getoDbl();
        $sql = "ALTER MATERIALIZED VIEW $nameView OWNER TO \"$this->sSchema\"";
        if (($oDbl->exec($sql)) === false) {
            $sClauError = 'Refresh';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
    }

    private function getIdSchemasGrupStgr()
    {
        $RegionStgr = $this->sRegionStgr;
        $gesDl = new GestorDelegacion();
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        return $gesDl->getArrayIdSchemaRegionStgr($RegionStgr, $mi_sfsv);
    }

    private function getSchemasGrupStgr()
    {
        $RegionStgr = $this->sRegionStgr;
        $gesDl = new GestorDelegacion();
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        return $gesDl->getArraySchemasRegionStgr($RegionStgr, $mi_sfsv);
    }

    private function getSchemasComunGrupStgr()
    {
        $RegionStgr = $this->sRegionStgr;
        $gesDl = new GestorDelegacion();

        return $gesDl->getArraySchemasRegionStgr($RegionStgr, NULL);
    }

    private function getDefView($view, $comun = FALSE)
    {
        // Excepciones
        switch ($view) {
            case 'av_actividades':
                $sql_def_view = $this->getDefViewActividades();
                break;
            case 'd_asistentes_out':
                $sql_def_view = $this->getDefViewAsistentesOut();
                break;
            case 'd_asistentes_dl':
                $sql_def_view = $this->getDefViewAsistentesDl();
                break;
            case 'd_cargos_activ_dl':
                $sql_def_view = $this->getDefViewCargosActivDl();
                break;

            default:
                if ($comun) {
                    $a_schemas = $this->getSchemasComunGrupStgr();
                } else {
                    $a_schemas = $this->getSchemasGrupStgr();
                }

                $schema1 = current($a_schemas);
                $columns = $this->getNameColumns($schema1, $view);

                $sql_def_view = '';
                foreach ($a_schemas as $id_dl => $schema) {
                    $sql_def_view .= empty($sql_def_view) ? '' : " UNION ALL ";
                    $sql_def_view .= "SELECT $columns, $id_dl AS id_dl FROM \"$schema\".$view ";
                }
        }
        return $sql_def_view;
    }

    private function getDefViewActividades()
    {
        $a_schemas = $this->getSchemasComunGrupStgr();
        $columns = $this->getNameColumns('public', "av_actividades_pub");

        $list_dl = '';
        foreach ($a_schemas as $schema) {
            $a_reg = explode('-', $schema);
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= "'$a_reg[1]'::character varying";
        }
        $where = "(av_actividades_pub.dl_org)::text = any ((array[$list_dl])::text[])";
        return "SELECT $columns FROM av_actividades_pub WHERE ($where)";
    }

    private function getDefViewAsistentesOut()
    {
        $a_schemas = $this->getIdSchemasGrupStgr();
        $columns = $this->getNameColumns('publicv', "d_asistentes_de_paso");

        $list_dl = '';
        foreach ($a_schemas as $id) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= "$id";
        }
        $where = "d_asistentes_de_paso.id_schema = any (array[$list_dl])";
        return "SELECT $columns FROM publicv.d_asistentes_de_paso WHERE ($where)";
    }

    private function getDefViewAsistentesDl()
    {
        $a_schemas = $this->getIdSchemasGrupStgr();
        $columns = $this->getNameColumns('global', "d_asistentes_dl");

        $list_dl = '';
        foreach ($a_schemas as $id) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= "$id";
        }
        $where = "d_asistentes_dl.id_schema = any (array[$list_dl])";
        return "SELECT $columns FROM global.d_asistentes_dl WHERE ($where)";
    }

    private function getDefViewCargosActivDl()
    {
        $a_schemas = $this->getIdSchemasGrupStgr();
        $columns = $this->getNameColumns('global', "d_cargos_activ");

        $list_dl = '';
        foreach ($a_schemas as $id) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= "$id";
        }
        $where = "d_cargos_activ.id_schema = any (array[$list_dl])";
        return "SELECT $columns FROM global.d_cargos_activ WHERE ($where)";
    }

    private function getNameColumns($schema1, $view)
    {
        $oDbl = $this->getoDbl();
        // coger la primera dl como referencia para el nombre de los campos

        $definicion = '';

        $sQuery = "SELECT column_name
	               FROM information_schema.columns
	               WHERE table_schema = '$schema1'
	               AND table_name = '$view' ";

        foreach ($oDbl->query($sQuery) as $row) {
            $column_name = $row['column_name'];
            //if ($column_name == 'id_schema') { continue; }
            $definicion .= "$view.$column_name, ";
        }
        // borrar la última coma
        $definicion = substr($definicion, 0, -2);
        return $definicion;
    }

    private function getSqlView($view)
    {
        $oDbl = $this->getoDbl();
        $schemaName = "$this->sSchema";
        $definicion = '';
        //SELECT definition FROM pg_matviews WHERE schemaname='H-Hv' AND matviewname='d_profesor_stgr';

        $sQuery = "SELECT definition 
                FROM pg_matviews 
                WHERE schemaname='$schemaName' AND matviewname='$view'; 
                ";
        foreach ($oDbl->query($sQuery) as $row) {
            $definicion = $row['definition'];
        }

        // borrar el último punto y coma
        $definicion = substr($definicion, 0, -1);
        return $definicion;
    }
}
