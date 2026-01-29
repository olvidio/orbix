<?php

namespace actividadessacd\model;

use core\ConfigGlobal;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class ActividadesSacdFunciones
{

    private array $a_txt = [];

    public function getArrayTraducciones($idioma): array|false
    {
        $idioma = empty($idioma) ? 'es' : $idioma;
        if (empty($this->a_txt[$idioma])) {
            $ActividadSacdTextoRepository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
            $cAtnActivSacdTextos = $ActividadSacdTextoRepository->getActividadSacdTextos([]);
            foreach ($cAtnActivSacdTextos as $oAtnActivSacdTexto) {
                $clave = $oAtnActivSacdTexto->getClave();
                $idioma = $oAtnActivSacdTexto->getIdioma();
                $texto = $oAtnActivSacdTexto->getTexto();
                $this->a_txt[$idioma][$clave] = $texto;
            }
        }
        if (empty($this->a_txt[$idioma])) {
            $str = sprintf(_("No existe el idioma: %s. Debe corregirlo en la ficha del sacd"), $idioma);
            echo $str . "<br>";
            return FALSE;
        }
        return $this->a_txt[$idioma];
    }

    public function getTraduccion($clave, $idioma): string
    {
        $a_traduccion = $this->getArrayTraducciones($idioma);
        if (!empty($a_traduccion[$clave])) {
            $txt_traduccion = $a_traduccion[$clave];
        } else {
            // El idioma por defecto (es) debería existir siempre
            $a_traduccion = $this->getArrayTraducciones('es');
            if (!empty($a_traduccion[$clave])) {
                $txt_traduccion = $a_traduccion[$clave];
            } else {
                $txt_traduccion = sprintf(_("falta definir el texto %s en este idioma: %s"), $clave, $idioma);
            }
        }
        return $txt_traduccion;
    }


    public function getLugar_dl(): ?string
    {
        if (ConfigGlobal::is_dmz()) {
            return "xxxx";
        }
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $CentroDlRepository->getCentros(['tipo_ctr' => 'dl']);
        $num_dl = count($cCentros);
        switch ($num_dl) {
            case 0:
                // Puede ser el nombre de la región
                $cCentros = $CentroDlRepository->getCentros(['tipo_ctr' => 'cr']);
                if (count($cCentros) > 0) {
                    $oCentro = $cCentros[0];
                } else {
                    // No existe el nombre de la delegación ni región.
                    return '?';
                }
                break;
            case 1:
                $oCentro = $cCentros[0];
                break;
            default:
                // más de una dl?
                exit (_("Más de un centro definido como dl"));
        }
        // Buscar la dirección
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