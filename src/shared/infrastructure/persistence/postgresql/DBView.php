<?php

namespace src\shared\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;


class DBView
{
    protected PDO $oDbl;
    protected string $sSchema = '';
    protected string $sRegionStgr = '';
    protected string $sView = '';

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     */
    function __construct(string $schema, ?int $mi_sfsv, string $db)
    {
        // Necesito permisos de superusuario para poder acceder a los distintos esquemas
        // que pertenecen a la region del stgr.

        $oConfigDB = new ConfigDB('importar');
        $config = null;
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
            case 'exterior_select':
                if ($mi_sfsv === 1) {
                    $config = $oConfigDB->getEsquema('publicv-e');
                } elseif ($mi_sfsv === 2) {
                    $config = $oConfigDB->getEsquema('publicf');
                }
                break;
            case 'comun_select':
                $config = $oConfigDB->getEsquema('public');
                break;
        }
        if ($config === null) {
            throw new \InvalidArgumentException(sprintf(_('Conexión DBView no válida: %s'), $db));
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

    public function setDbConexion(PDO $oDbl): void
    {
        $this->setoDbl($oDbl);
    }

    public function setSchema(string $schema): void
    {
        $this->sSchema = $schema;
    }

    public function setRegionStgr(string $regionStgr): void
    {
        $this->sRegionStgr = $regionStgr;
    }

    public function setView(string $view): void
    {
        $this->sView = $view;
    }

    protected function setoDbl(PDO $oDbl): void
    {
        $this->oDbl = $oDbl;
    }

    protected function getoDbl(): PDO
    {
        return $this->oDbl;
    }


    public function normalizarTexto(mixed $str): string
    {
        $str = trim(is_scalar($str) ? (string) $str : '');
        // saltos de linea y tabuladores
        $search = ["\n", "\t", "\r"];
        $replace = ' ';
        $new_str = str_replace($search, $replace, $str);
        // espacios extra:
        $new_str = preg_replace('/\s\s+/', ' ', $new_str);
        $lower = strtolower($new_str ?? '');

        $string = preg_replace('/[^[:print:]]/', '', $lower);

        return (string) $string;
    }

    public function ExisteYEsIgual(bool $comun = false): bool
    {
        // definición teórica
        $defNew = $this->getDefView($this->sView, $comun);
        // quitar espacios, tabuladores, returns...
        $defNew = $this->normalizarTexto($defNew);
        // definición real
        $defActual = $this->getSqlView($this->sView);
        $defActual = $this->normalizarTexto($defActual);

        if ($defActual == $defNew) {
            return true;
        }
        if (!empty($defActual)) { // SI existe, pero es distinta: la borro
            $this->Drop();
        }

        return false;
    }

    public function Create(bool $comun = false): bool
    {
        /*
         * OJO, hay que dar permisos...
         */
        $oDbl = $this->getoDbl();
        $nameView = " \"$this->sSchema\".$this->sView";

        $sql = "CREATE MATERIALIZED VIEW $nameView AS ";
        $sql .= $this->getDefView($this->sView, $comun);

        if ($oDbl->exec($sql) === false) {
            $sClauError = 'Refresh';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }
        $this->setOwnerToSchema($nameView);

        return true;
    }

    public function Drop(): bool
    {
        $oDbl = $this->getoDbl();
        $nameView = " \"$this->sSchema\".$this->sView";

        // Sólo puede el propietario. Por eso hay que emplear la conexión oDB
        $this->setOwnerToSuperuser($nameView);
        $sql = "DROP MATERIALIZED VIEW IF EXISTS $nameView CASCADE ";

        if ($oDbl->exec($sql) === false) {
            $sClauError = 'Drop';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }

        return true;
    }

    public function Refresh(): bool
    {
        $oDbl = $this->getoDbl();
        $nameView = " \"$this->sSchema\".$this->sView";

        // Sólo puede el propietario.
        $this->setOwnerToSuperuser($nameView);

        $sql = "REFRESH MATERIALIZED VIEW $nameView";
        if ($oDbl->exec($sql) === false) {
            $sClauError = 'Refresh';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }
        $this->setOwnerToSchema($nameView);

        return true;
    }

    private function setOwnerToSuperuser(string $nameView): void
    {
        $oDbl = $this->getoDbl();
        $sql = "ALTER MATERIALIZED VIEW $nameView OWNER TO \"orbix_admindb\"";
        if ($oDbl->exec($sql) === false) {
            $sClauError = 'Refresh';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
            }
        }
    }

    private function setOwnerToSchema(string $nameView): void
    {
        $oDbl = $this->getoDbl();
        $sql = "ALTER MATERIALIZED VIEW $nameView OWNER TO \"$this->sSchema\"";
        if ($oDbl->exec($sql) === false) {
            $sClauError = 'Refresh';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
            }
        }
    }

    /**
     * @return array<int|string, mixed>
     */
    private function getIdSchemasGrupStgr(): array
    {
        $RegionStgr = $this->sRegionStgr;
        $gesDl = DependencyResolver::get(DelegacionRepositoryInterface::class);
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        return $gesDl->getArrayIdSchemaRegionStgr($RegionStgr, $mi_sfsv);
    }

    /**
     * @return list<string>
     */
    private function getSchemasGrupStgr(): array
    {
        $RegionStgr = $this->sRegionStgr;
        $gesDl = DependencyResolver::get(DelegacionRepositoryInterface::class);
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $a_schemas = $gesDl->getArraySchemasRegionStgr($RegionStgr, $mi_sfsv);
        // quitar H-Hv
        if (($key = array_search("H-crHv", $a_schemas)) !== false) {
            unset($a_schemas[$key]);
        }
        if (($key = array_search("M-crMv", $a_schemas)) !== false) {
            unset($a_schemas[$key]);
        }

        return array_values($a_schemas);
    }

    /**
     * @return list<string>
     */
    private function getSchemasComunGrupStgr(): array
    {
        $RegionStgr = $this->sRegionStgr;
        $gesDl = DependencyResolver::get(DelegacionRepositoryInterface::class);

        $a_schemas = $gesDl->getArraySchemasRegionStgr($RegionStgr);

        // quitar H-Hv
        if (($key = array_search("H-crH", $a_schemas)) !== false) {
            unset($a_schemas[$key]);
        }
        if (($key = array_search("M-crM", $a_schemas)) !== false) {
            unset($a_schemas[$key]);
        }

        return array_values($a_schemas);
    }

    private function getDefView(string $view, bool $comun = false): string
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
                if ($schema1 === false) {
                    return '';
                }
                $columns = $this->getNameColumns((string) $schema1, $view);

                $sql_def_view = '';
                foreach ($a_schemas as $id_dl => $schema) {
                    $sql_def_view .= empty($sql_def_view) ? '' : " UNION ALL ";
                    $sql_def_view .= "SELECT $columns, $id_dl AS id_dl FROM \"$schema\".$view ";
                }
        }

        return $sql_def_view;
    }

    private function getDefViewActividades(): string
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
        return "SELECT $columns FROM public.av_actividades_pub WHERE ($where)";
    }

    private function getDefViewAsistentesOut(): string
    {
        $a_schemas = $this->getIdSchemasGrupStgr();
        $columns = $this->getNameColumns('publicv', "d_asistentes_de_paso");

        $list_dl = '';
        foreach ($a_schemas as $id) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= is_scalar($id) ? (string) $id : '';
        }
        $where = "d_asistentes_de_paso.id_schema = any (array[$list_dl])";
        return "SELECT $columns FROM publicv.d_asistentes_de_paso WHERE ($where)";
    }

    private function getDefViewAsistentesDl(): string
    {
        $a_schemas = $this->getIdSchemasGrupStgr();
        $columns = $this->getNameColumns('global', "d_asistentes_dl");

        $list_dl = '';
        foreach ($a_schemas as $id) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= is_scalar($id) ? (string) $id : '';
        }
        $where = "d_asistentes_dl.id_schema = any (array[$list_dl])";
        return "SELECT $columns FROM global.d_asistentes_dl WHERE ($where)";
    }

    private function getDefViewCargosActivDl(): string
    {
        $a_schemas = $this->getIdSchemasGrupStgr();
        $columns = $this->getNameColumns('global', "d_cargos_activ");

        $list_dl = '';
        foreach ($a_schemas as $id) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= is_scalar($id) ? (string) $id : '';
        }
        $where = "d_cargos_activ.id_schema = any (array[$list_dl])";
        return "SELECT $columns FROM global.d_cargos_activ WHERE ($where)";
    }

    private function getNameColumns(string $schema1, string $view): string
    {
        $oDbl = $this->getoDbl();
        // coger la primera dl como referencia para el nombre de los campos

        $definicion = '';

        $sQuery = "SELECT column_name
	               FROM information_schema.columns
	               WHERE table_schema = '$schema1'
	               AND table_name = '$view' ";

        $st = $oDbl->query($sQuery);
        if ($st === false) {
            return '';
        }
        foreach ($st as $row) {
            if (!is_array($row) || !isset($row['column_name'])) {
                continue;
            }
            $column_name = is_scalar($row['column_name']) ? (string) $row['column_name'] : '';
            if ($column_name === '') {
                continue;
            }
            $definicion .= "$view.$column_name, ";
        }
        // borrar la última coma
        if ($definicion === '') {
            return '';
        }

        return substr($definicion, 0, -2);
    }

    private function getSqlView(string $view): string
    {
        $oDbl = $this->getoDbl();
        $schemaName = "$this->sSchema";
        $definicion = '';
        //SELECT definition FROM pg_matviews WHERE schemaname='H-Hv' AND matviewname='d_profesor_stgr';

        $sQuery = "SELECT definition 
                FROM pg_matviews 
                WHERE schemaname='$schemaName' AND matviewname='$view'; 
                ";
        $st = $oDbl->query($sQuery);
        if ($st === false) {
            return '';
        }
        foreach ($st as $row) {
            if (is_array($row) && isset($row['definition']) && is_scalar($row['definition'])) {
                $definicion = (string) $row['definition'];
            }
        }

        if ($definicion === '') {
            return '';
        }
        // borrar el último punto y coma
        return substr($definicion, 0, -1);
    }
}
