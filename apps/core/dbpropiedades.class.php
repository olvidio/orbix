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
        $txt = '';
        // Lista de posibles esquemas (en comun)
        $oConfig = new Config('comun');
        $config = $oConfig->getEsquema('public'); 
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
                $sv = $row[0].'v';
                if (!empty($default) && $sv == $default) { $sel_sv = 'selected'; } else { $sel_sv = ''; }
                $txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
                // 7.3.2019 Parece que sf va por su lado.
                //$sf = $row[0].'f';
                //if (!empty($default) && $sf == $default) { $sel_sf = 'selected'; } else { $sel_sf = ''; }
                //$txt .= "<option value=\"$sf\" $sel_sf>$sf</option>";
            }
        }
        return $txt;
    }

    public function array_posibles_esquemas() {
        $a_esquemas = [];
        // Lista de posibles esquemas (en comun)
        $oConfig = new Config('comun');
        $config = $oConfig->getEsquema('public'); 
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
                $sv = $row[0].'v';
                $a_esquemas[$sv] = $sv;
                // 7.3.2019 Parece que sf va por su lado.
                //$sf = $row[0].'f';
                //$a_esquemas[$sf] = $sf;
            }
        }
        return $a_esquemas;
    }

}