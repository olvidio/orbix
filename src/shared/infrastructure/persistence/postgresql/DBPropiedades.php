<?php

namespace src\shared\infrastructure\persistence\postgresql;

use PDO;
use src\shared\config\ServerConf;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

class DBPropiedades
{

    private bool $Blanco = false;

    public function setBlanco(mixed $blanco = false): void
    {
        $this->Blanco = (bool) $blanco;
    }

    public function posibles_esquemas(string $default = '', bool $comun = false): string
    {
        $txt = "<select id=\"esquema\" name=\"esquema\" >";
        if ($this->Blanco) {
            $txt .= "<option></option>";
        }
        $txt .= $this->opciones_posibles_esquemas($default, $comun);
        $txt .= '</select>';
        return $txt;
    }

    public function opciones_posibles_esquemas(string $default = '', bool $comun = false): string
    {
        /* Para el caso de sf, entrando como en el directorio orbixsf, al redirigir
         * a orbix, la ubicación acaba siendo sv.
         * solo me sirve getenv('UBICACION'); para la entrada.
         * una vez dentro, debo usar el usuario para saber si es sv o sf
         */
        if (empty($GLOBALS['user_sfsv'])) {
            $ubicacion = getenv('UBICACION');
        } else {
            if ($GLOBALS['user_sfsv'] == 1) {
                $ubicacion = 'sv';
            } else {
                $ubicacion = 'sf';
            }
        }
        if (empty($ubicacion)) {
            $ubicacion = 'sv';
        }
        $txt = '';
        // Lista de posibles esquemas (en comun)
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();

        $sQuery = "select nspname from pg_namespace where nspowner > 1000 AND nspname !~ '^zz' ORDER BY nspname";
        $oDblSt = $oDBP->query($sQuery);
        if ($oDblSt === false) {
            $sClauError = 'Schemas.lista';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDBP, $sClauError, (string) __LINE__, __FILE__);
            }
            return '';
        }
        foreach ($oDblSt as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nspname = $this->pdoCellToString($row[0] ?? null);
            if ($nspname === '') {
                continue;
            }
            if ($nspname === 'public') {
                continue;
            }
            if ($nspname === 'resto') {
                continue;
            }
            if ($nspname === 'global') {
                continue;
            }
            if ($nspname === 'bucardo') {
                continue;
            }
            if ($comun) {
                $sv = $nspname;
                if (!empty($default) && $sv == $default) {
                    $sel_sv = 'selected';
                } else {
                    $sel_sv = '';
                }
                $txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
            } else {
                if ($ubicacion == 'sv') {
                    $sv = $nspname . 'v';
                    if (!empty($default) && $sv == $default) {
                        $sel_sv = 'selected';
                    } else {
                        $sel_sv = '';
                    }
                    $txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
                }
                // 7.3.2019 Parece que sf va por su lado.
                if ($ubicacion == 'sf') {
                    $sf = $nspname . 'f';
                    if (!empty($default) && $sf == $default) {
                        $sel_sf = 'selected';
                    } else {
                        $sel_sf = '';
                    }
                    $txt .= "<option value=\"$sf\" $sel_sf>$sf</option>";
                }
            }
        }
        return $txt;
    }

    /**
     * @return array<string, string>|false
     */
    public function array_posibles_esquemas(bool $comun = false, bool $rstgr = false): array|false
    {
        /* Para el caso de sf, entrando como en el directorio orbixsf, al redirigir
         * a orbix, la ubicación acaba siendo sv.
         * solo me sirve   getenv('UBICACION'); para la entrada.
         * una vez dentro, debo usar el usuario para saber si es sv o sf
         */
        if (empty($GLOBALS['user_sfsv'])) {
            $ubicacion = getenv('UBICACION');
        } else {
            if ($GLOBALS['user_sfsv'] == 1) {
                $ubicacion = 'sv';
            } else {
                $ubicacion = 'sf';
            }
        }
        // si falla quedarse en sv (en los tests)
        if (empty($ubicacion)) {
            $ubicacion = 'sv';
        }
        $a_esquemas = [];
        // Lista de posibles esquemas (en comun)
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();

        $sQuery = "select nspname from pg_namespace where nspowner > 1000 AND nspname !~ '^zz' ORDER BY nspname";
        $oDblSt = $oDBP->query($sQuery);
        if ($oDblSt === false) {
            $sClauError = 'Schemas.lista';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDBP, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }
        foreach ($oDblSt as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nspname = $this->pdoCellToString($row[0] ?? null);
            if ($nspname === '') {
                continue;
            }
            if ($nspname === 'public') {
                continue;
            }
            if ($nspname === 'resto') {
                continue;
            }
            if ($nspname === 'global') {
                continue;
            }
            if ($nspname === 'bucardo') {
                continue;
            }
            // Para los esquemas tipo 'H-H' o 'H-Hf',
            if (!$rstgr) {
                $a_reg = explode('-', $nspname);
                if (isset($a_reg[1]) && $a_reg[0] === $a_reg[1]) {
                    continue;
                }
            }
            if ($comun) {
                $sv = $nspname;
                $a_esquemas[$sv] = $sv;
            } else {
                if ($ubicacion === 'sv') {
                    $sv = $nspname . 'v';
                    $a_esquemas[$sv] = $sv;
                }
                // 7.3.2019 Parece que sf va por su lado.
                if ($ubicacion === 'sf') {
                    $sf = $nspname . 'f';
                    $a_esquemas[$sf] = $sf;
                }
            }
        }
        return $a_esquemas;
    }

    /**
     * @return array<string, string>
     */
    public function array_posibles_dl_de_esquemas(bool $comun = false, bool $rstgr = false): array
    {
        $a_posibles_esquemas = $this->array_posibles_esquemas($comun, $rstgr);
        $a_dl = [];
        if (!is_array($a_posibles_esquemas)) {
            return $a_dl;
        }
        foreach ($a_posibles_esquemas as $esquema) {
            $region = strtok($esquema, '-');
            $dl = strtok('-');
            if (is_string($dl)) {
                $a_dl[$dl] = $dl;
            }
        }
        return $a_dl;
    }

    public function posibles_tablas(string $default = ''): string
    {
        $txt = "<select id=\"tabla\" name=\"tabla\" >";
        if ($this->Blanco) {
            $txt .= "<option></option>";
        }
        $a_tablas = $this->array_posibles_tablas();
        if (!is_array($a_tablas)) {
            $a_tablas = [];
        }
        foreach ($a_tablas as $tabla) {
            if (!empty($default) && $tabla == $default) {
                $sel_tabla = 'selected';
            } else {
                $sel_tabla = '';
            }
            $txt .= "<option value=\"$tabla\" $sel_tabla>$tabla</option>";
        }
        $txt .= '</select>';
        return $txt;
    }

    /**
     * @return list<string>|false
     */
    public function array_posibles_tablas(): array|false
    {
        $esquema = "H-dlbv";
        $a_tablas = [];
        // Lista de posibles tablas (en sv)
        $oConfigDB = new ConfigDB('sv');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $sQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema ='$esquema' ORDER BY table_name";
        $oDblSt = $oDBP->query($sQuery);
        if ($oDblSt === false) {
            $sClauError = 'Schemas.lista';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDBP, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }
        foreach ($oDblSt as $row) {
            if (!is_array($row) || !isset($row[0])) {
                continue;
            }
            $tabla = $this->pdoCellToString($row[0]);
            if ($tabla !== '') {
                $a_tablas[] = $tabla;
            }
        }
        return $a_tablas;
    }

    /**
     * @return list<string>|false
     */
    public function array_esquemas_con_tabla(string $tabla): array|false
    {
        $a_esquemas = [];
        // Lista de posibles tablas (en sv)
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $sQuery = "SELECT table_schema FROM information_schema.tables WHERE table_name ='$tabla' ORDER BY table_schema";
        $oDblSt = $oDBP->query($sQuery);
        if ($oDblSt === false) {
            $sClauError = 'Schemas.lista';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDBP, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }
        foreach ($oDblSt as $row) {
            if (!is_array($row) || !isset($row[0])) {
                continue;
            }
            $schema = $this->pdoCellToString($row[0]);
            if ($schema === '') {
                continue;
            }
            if ($schema === 'public') {
                continue;
            }
            if ($schema === 'resto') {
                continue;
            }
            if ($schema === 'global') {
                continue;
            }
            if ($schema === 'bucardo') {
                continue;
            }
            $a_esquemas[] = $schema;
        }
        return $a_esquemas;
    }

    /**
     * @return string|false
     */
    public function getIdSchema(string $esquema): string|false
    {
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $sQuery = "SELECT idschema('$esquema'::text) ";
        $oDblSt = $oDBP->query($sQuery);
        if ($oDblSt === false) {
            $sClauError = 'Schemas.lista';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDBP, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }
        $id_esquema = '';
        foreach ($oDblSt as $row) {
            if (is_array($row)) {
                $id_esquema = $this->pdoCellToString($row[0] ?? null);
            }
        }
        if (!empty($id_esquema)) {
            return $id_esquema;
        }

        return false;
    }

    /**
     * Nombres base de esquema (región-dl) presentes en cualquiera de las conexiones de importar
     * (comun, réplicas, sv, sv-e, sf), para desplegables donde hace falta el origen aunque ya no esté en comun.
     *
     * @return array<string, string> mapa base => base
     */
    public function array_esquemas_union_importar(): array
    {
        $bases = [];
        $isDocker = (bool) preg_match('/(.*?)\.docker/', ServerConf::SERVIDOR);
        $labels = ['public', 'publicv', 'publicv-e', 'publicf'];
        if (!$isDocker) {
            $labels = ['public', 'public_select', 'publicv', 'publicv-e', 'publicv-e_select', 'publicf'];
        }
        $oImportar = new ConfigDB('importar');
        foreach ($labels as $label) {
            try {
                $config = $oImportar->getEsquema($label);
                $pdo = (new DBConnection($config))->getPDO();
            } catch (\Throwable) {
                continue;
            }
            foreach ($this->schemasDesdePdo($pdo) as $nspname) {
                $base = $this->nombreBaseEsquemaDesdeNs($nspname);
                if ($base === '') {
                    continue;
                }
                $a_reg = explode('-', $base, 2);
                if (isset($a_reg[1]) && $a_reg[0] === $a_reg[1]) {
                    continue;
                }
                $bases[$base] = $base;
            }
        }
        ksort($bases, SORT_NATURAL | SORT_FLAG_CASE);

        return $bases;
    }

    private function nombreBaseEsquemaDesdeNs(string $nspname): string
    {
        $n = $nspname;
        $last = substr($n, -1);
        if (($last === 'v' || $last === 'f') && strlen($n) > 1) {
            return substr($n, 0, -1);
        }

        return $n;
    }

    /**
     * @return list<string>
     */
    private function schemasDesdePdo(PDO $pdo): array
    {
        $out = [];
        $sQuery = "select nspname from pg_namespace where nspowner > 1000 AND nspname !~ '^zz' ORDER BY nspname";
        $oDblSt = $pdo->query($sQuery);
        if ($oDblSt === false) {
            return $out;
        }
        foreach ($oDblSt as $row) {
            if (!is_array($row)) {
                continue;
            }
            $nspname = $this->pdoCellToString($row[0] ?? null);
            if ($nspname === '') {
                continue;
            }
            if ($nspname === 'public' || $nspname === 'resto' || $nspname === 'global' || $nspname === 'bucardo') {
                continue;
            }
            $out[] = $nspname;
        }

        return $out;
    }

    private function pdoCellToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return '';
    }

}
