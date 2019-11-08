<?php
namespace core;

class DBPropiedades {


    public function posibles_esquemas($default='') {
        $txt = "<select id=\"esquema\" name=\"esquema\" >";
        $txt .= $this->opciones_posibles_esquemas($default);
        $txt .= '</select>';
        return $txt;
    }

    public function opciones_posibles_esquemas($default='') {
        /* Para el caso de sf, entrando como en el directorio orbixsf, al redirigir
         * a orbix, la ubicación acaba siendo sv.
         * solo me sirve   $_SERVER['UBICACION']; para la entrada.
         * una vez dentro, debo usar el usuario para saber si es sv o sf
         */
        if (empty($GLOBALS['user_sfsv'])) {
            $ubicacion = $_SERVER['UBICACION'];
        } else {
            if ($GLOBALS['user_sfsv'] == 1) {
                $ubicacion = 'sv';
            } else {
                $ubicacion = 'sf';
            }
        }
        $txt = '';
        // Lista de posibles esquemas (en comun)
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public'); 
        $oConexion = new dbConnection($config);
        $oDBP = $oConexion->getPDO();

        $sQuery = "select nspname from pg_namespace where nspowner > 1000 ORDER BY nspname";
        if (($oDblSt = $oDBP->query($sQuery)) === false) {
            $sClauError = 'Schemas.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (is_object($oDblSt)) {
            $oDblSt->execute();
            foreach($oDblSt as $row) {
                if (!isset($row[1])) { $a = 0; } else { $a = 1; } // para el caso de sólo tener un valor.
                if ($row[0] == 'public') continue;
                if ($row[0] == 'resto') continue;
                if ($row[0] == 'global') continue;
                if ($ubicacion == 'sv') {
                    $sv = $row[0].'v';
                    if (!empty($default) && $sv == $default) { $sel_sv = 'selected'; } else { $sel_sv = ''; }
                    $txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
                }
                // 7.3.2019 Parece que sf va por su lado.
                if ($ubicacion == 'sf') {
                    $sf = $row[0].'f';
                    if (!empty($default) && $sf == $default) { $sel_sf = 'selected'; } else { $sel_sf = ''; }
                    $txt .= "<option value=\"$sf\" $sel_sf>$sf</option>";
                }
            }
        }
        return $txt;
    }

    public function array_posibles_esquemas() {
        /* Para el caso de sf, entrando como en el directorio orbixsf, al redirigir
         * a orbix, la ubicación acaba siendo sv.
         * solo me sirve   $_SERVER['UBICACION']; para la entrada.
         * una vez dentro, debo usar el usuario para saber si es sv o sf
         */
        if (empty($GLOBALS['user_sfsv'])) {
            $ubicacion = $_SERVER['UBICACION'];
        } else {
            if ($GLOBALS['user_sfsv'] == 1) {
                $ubicacion = 'sv';
            } else {
                $ubicacion = 'sf';
            }
        }
        $a_esquemas = [];
        // Lista de posibles esquemas (en comun)
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public'); 
        $oConexion = new dbConnection($config);
        $oDBP = $oConexion->getPDO();

        $sQuery = "select nspname from pg_namespace where nspowner > 1000 ORDER BY nspname";
        if (($oDblSt = $oDBP->query($sQuery)) === false) {
            $sClauError = 'Schemas.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (is_object($oDblSt)) {
            $oDblSt->execute();
            foreach($oDblSt as $row) {
                if ($row[0] == 'public') continue;
                if ($row[0] == 'resto') continue;
                if ($row[0] == 'global') continue;
                if ($row[0] == 'bucardo') continue;
                if ($ubicacion == 'sv') {
                    $sv = $row[0].'v';
                    $a_esquemas[$sv] = $sv;
                }
                // 7.3.2019 Parece que sf va por su lado.
                if ($ubicacion == 'sf') {
                    $sf = $row[0].'f';
                    $a_esquemas[$sf] = $sf;
                }
            }
        }
        return $a_esquemas;
    }

}