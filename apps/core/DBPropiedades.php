<?php

namespace core;

class DBPropiedades
{

    var $bBlanco = FALSE;

    public function setBlanco($blanco = FALSE)
    {
        if ($blanco) {
            $this->bBlanco = TRUE;
        } else {
            $this->bBlanco = FALSE;
        }
    }

    public function posibles_esquemas($default = '', $comun = FALSE)
    {
        $txt = "<select id=\"esquema\" name=\"esquema\" >";
        if ($this->bBlanco) {
            $txt .= "<option></option>";
        }
        $txt .= $this->opciones_posibles_esquemas($default, $comun);
        $txt .= '</select>';
        return $txt;
    }

    public function opciones_posibles_esquemas($default = '', $comun = FALSE)
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
        $txt = '';
        // Lista de posibles esquemas (en comun)
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();

        $sQuery = "select nspname from pg_namespace where nspowner > 1000 AND nspname !~ '^zz' ORDER BY nspname";
        if (($oDblSt = $oDBP->query($sQuery)) === false) {
            $sClauError = 'Schemas.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (is_object($oDblSt)) {
            $oDblSt->execute();
            foreach ($oDblSt as $row) {
                if (!isset($row[1])) {
                    $a = 0;
                } else {
                    $a = 1;
                } // para el caso de sólo tener un valor.
                if ($row[0] == 'public') continue;
                if ($row[0] == 'resto') continue;
                if ($row[0] == 'global') continue;
                if ($row[0] == 'bucardo') continue;
                if ($comun) {
                    $sv = $row[0];
                    if (!empty($default) && $sv == $default) {
                        $sel_sv = 'selected';
                    } else {
                        $sel_sv = '';
                    }
                    $txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
                } else {
                    if ($ubicacion == 'sv') {
                        $sv = $row[0] . 'v';
                        if (!empty($default) && $sv == $default) {
                            $sel_sv = 'selected';
                        } else {
                            $sel_sv = '';
                        }
                        $txt .= "<option value=\"$sv\" $sel_sv>$sv</option>";
                    }
                    // 7.3.2019 Parece que sf va por su lado.
                    if ($ubicacion == 'sf') {
                        $sf = $row[0] . 'f';
                        if (!empty($default) && $sf == $default) {
                            $sel_sf = 'selected';
                        } else {
                            $sel_sf = '';
                        }
                        $txt .= "<option value=\"$sf\" $sel_sf>$sf</option>";
                    }
                }
            }
        }
        return $txt;
    }

    public function array_posibles_esquemas($comun = FALSE, $rstgr = FALSE)
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
        $a_esquemas = [];
        // Lista de posibles esquemas (en comun)
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();

        $sQuery = "select nspname from pg_namespace where nspowner > 1000 AND nspname !~ '^zz' ORDER BY nspname";
        if (($oDblSt = $oDBP->query($sQuery)) === false) {
            $sClauError = 'Schemas.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (is_object($oDblSt)) {
            $oDblSt->execute();
            foreach ($oDblSt as $row) {
                if ($row[0] == 'public') {
                    continue;
                }
                if ($row[0] == 'resto') {
                    continue;
                }
                if ($row[0] == 'global') {
                    continue;
                }
                if ($row[0] == 'bucardo') {
                    continue;
                }
                // Para los esquemas tipo 'H-H' o 'H-Hf',
                if (!$rstgr) {
                    $a_reg = explode('-', $row[0]);
                    if ($a_reg[0] == $a_reg[1]) {
                        continue;
                    }
                }
                if ($comun) {
                    $sv = $row[0];
                    $a_esquemas[$sv] = $sv;
                } else {
                    if ($ubicacion == 'sv') {
                        $sv = $row[0] . 'v';
                        $a_esquemas[$sv] = $sv;
                    }
                    // 7.3.2019 Parece que sf va por su lado.
                    if ($ubicacion == 'sf') {
                        $sf = $row[0] . 'f';
                        $a_esquemas[$sf] = $sf;
                    }
                }
            }
        }
        return $a_esquemas;
    }

    public function posibles_tablas($default = '')
    {
        $txt = "<select id=\"tabla\" name=\"tabla\" >";
        if ($this->bBlanco) {
            $txt .= "<option></option>";
        }
        $a_tablas = $this->array_posibles_tablas();
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

    public function array_posibles_tablas()
    {
        $esquema = "H-dlbv";
        $a_tablas = [];
        // Lista de posibles tablas (en sv)
        $oConfigDB = new ConfigDB('sv');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $sQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema ='$esquema' ORDER BY table_name";
        if (($oDblSt = $oDBP->query($sQuery)) === false) {
            $sClauError = 'Schemas.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (is_object($oDblSt)) {
            $oDblSt->execute();
            foreach ($oDblSt as $row) {
                $tabla = $row[0];
                $a_tablas[] = $tabla;
            }
        }
        return $a_tablas;
    }

    public function array_esquemas_con_tabla($tabla)
    {
        $a_esquemas = [];
        // Lista de posibles tablas (en sv)
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $sQuery = "SELECT table_schema FROM information_schema.tables WHERE table_name ='$tabla' ORDER BY table_schema";
        if (($oDblSt = $oDBP->query($sQuery)) === false) {
            $sClauError = 'Schemas.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (is_object($oDblSt)) {
            $oDblSt->execute();
            foreach ($oDblSt as $row) {
                if ($row[0] == 'public') continue;
                if ($row[0] == 'resto') continue;
                if ($row[0] == 'global') continue;
                if ($row[0] == 'bucardo') continue;
                $esquema = $row[0];
                $a_esquemas[] = $esquema;
            }
        }
        return $a_esquemas;
    }

    public function getIdSchema($esquema)
    {
        $oConfigDB = new ConfigDB('comun');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $sQuery = "SELECT idschema('$esquema'::text) ";
        if (($oDblSt = $oDBP->query($sQuery)) === false) {
            $sClauError = 'Schemas.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $oDblSt->execute();
        $id_esquema = '';
        foreach ($oDblSt as $row) {
            $id_esquema = $row[0];
        }
        if (!empty($id_esquema)) {
            return $id_esquema;
        } else {
            return false;
        }
    }

}