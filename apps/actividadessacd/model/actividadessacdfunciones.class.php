<?php
namespace actividadessacd\model;

use actividadessacd\model\entity\GestorAtnActivSacdTexto;
use ubis\model\entity\GestorCentroDl;

class ActividadesSacdFunciones {
    
    /**
     * 
     * @var array
     */
    private $a_txt = [];
    
    function getArrayTraducciones($idioma) {
        $idioma = empty($idioma)? 'es' : $idioma;
        if (empty($this->a_txt[$idioma])) {
            $oGesAtnActivSacdTextos = new GestorAtnActivSacdTexto();
            $cAtnActivSacdTextos = $oGesAtnActivSacdTextos->getAtnActivSacdTextos();
            foreach ($cAtnActivSacdTextos as $oAtnActivSacdTexto) {
                $clave = $oAtnActivSacdTexto->getClave();
                $idioma = $oAtnActivSacdTexto->getIdioma();
                $texto = $oAtnActivSacdTexto->getTexto();
                $this->a_txt[$idioma][$clave] = $texto;
            }
        }
        if (empty($this->a_txt[$idioma])) {
            $str = sprintf(_("No existe este idoma: %s. Debe corregirlo en la ficha del sacd"),$idioma);
            echo $str;
            return FALSE;
        }
        return $this->a_txt[$idioma];
    }
    function getTraduccion($clave,$idioma) {
        $a_traduccion = $this->getArrayTraducciones($idioma);
        if (!empty($a_traduccion[$clave])) {
            $txt_traduccion = $a_traduccion[$clave];
        } else {
            // El idioma por defecto (es) debería existir siempre
            $a_traduccion = $this->getArrayTraducciones('es');
            if (!empty($a_traduccion[$clave])) {
                $txt_traduccion = $a_traduccion[$clave];
            } else {
                echo sprintf(_("falta definir el texto %s en este idioma: %s"),$clave,$idioma);
            }
        }
        return $txt_traduccion;
    }
    
    
    function getLugar_dl() {
        $oGesCentrosDl = new GestorCentroDl();
        $cCentros = $oGesCentrosDl->getCentros(['tipo_ctr' => 'dl']);
        $num_dl = count($cCentros);
        switch ($num_dl) {
            case 0:
                // Puede ser el nombre de la región
                $cCentros = $oGesCentrosDl->getCentros(['tipo_ctr' => 'cr']);
                if (count($cCentros) > 0) {
                    $oCentro = $cCentros[0];
                } else {
                    // No existe el nombre de la delegacion ni región.
                    return '?';
                }
                break;
            case 1:
                $oCentro = $cCentros[0];
                break;
            default:
                // más de una dl?
                exit (_("Más de un centro definido como dl"));
                break;
        }
        // Buscar la direccion
        $cDirecciones = $oCentro->getDirecciones();
        
        $poblacion = '';
        if (is_array($cDirecciones) & !empty($cDirecciones)) {
            $d = 0;
            foreach ($cDirecciones as $oDireccion) {
                $d++;
                if ($d > 1) {
                    $poblacion .= '<br>';
                }
                $poblacion .= $oDireccion->getPoblacion();
            }
        } else {
            exit (_("falta poner la dirección a la dl"));
        }
        return $poblacion; 
    }
}