<?php

namespace src\actividadessacd\application\services;

use core\ConfigGlobal;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Helpers para la comunicacion a los sacd:
 *  - `getTraduccion(clave, idioma)` devuelve el texto asociado, con
 *    fallback al idioma `es` si no existe, y texto de error si tampoco.
 *  - `getLugar_dl()` devuelve la poblacion de la delegacion del schema
 *    actual (para la firma "lugar, fecha" en el mail/impresion).
 *
 * Sucesor de `actividadessacd\model\ActividadesSacdFunciones`.
 */
final class ActividadesSacdHelper
{
    /** @var array<string, array<string,string>> cache idioma => clave => texto */
    private array $a_txt = [];

    /**
     * @return array<string,string>|false
     */
    public function getArrayTraducciones(string $idioma): array|bool
    {
        $idioma = $idioma === '' ? 'es' : $idioma;
        if (empty($this->a_txt[$idioma])) {
            $ActividadSacdTextoRepository = $GLOBALS['container']->get(ActividadSacdTextoRepositoryInterface::class);
            $cTextos = $ActividadSacdTextoRepository->getActividadSacdTextos([]);
            foreach ($cTextos as $oTexto) {
                $clave = $oTexto->getClave();
                $lang = $oTexto->getIdioma();
                $this->a_txt[$lang][$clave] = $oTexto->getTexto();
            }
        }
        if (empty($this->a_txt[$idioma])) {
            // No forzamos salida; el caller decide si mostrar aviso al usuario.
            return false;
        }
        return $this->a_txt[$idioma];
    }

    public function getTraduccion(string $clave, ?string $idioma): string
    {
        $idioma = $idioma ?? '';
        $a_traduccion = $this->getArrayTraducciones($idioma);
        if (is_array($a_traduccion) && !empty($a_traduccion[$clave])) {
            return $a_traduccion[$clave];
        }
        // fallback al idioma por defecto
        $a_traduccion = $this->getArrayTraducciones('es');
        if (is_array($a_traduccion) && !empty($a_traduccion[$clave])) {
            return $a_traduccion[$clave];
        }
        return sprintf(_("falta definir el texto %s en este idioma: %s"), $clave, $idioma);
    }

    /**
     * Poblacion(es) de la delegacion para la firma del mail/impresion.
     * Devuelve `xxxx` si esta en DMZ. Si hay mas de una direccion, las
     * separa por `<br>`. Devuelve `'?'` si no hay dl ni cr definido.
     */
    public function getLugar_dl(): string
    {
        if (ConfigGlobal::is_dmz()) {
            return 'xxxx';
        }
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $CentroDlRepository->getCentros(['tipo_ctr' => 'dl']);
        $num_dl = count($cCentros);
        $oCentro = null;
        switch ($num_dl) {
            case 0:
                $cCentros = $CentroDlRepository->getCentros(['tipo_ctr' => 'cr']);
                if (count($cCentros) > 0) {
                    $oCentro = $cCentros[0];
                } else {
                    return '?';
                }
                break;
            case 1:
                $oCentro = $cCentros[0];
                break;
            default:
                // mas de una dl: situacion anomala, se refleja en el texto
                return _("Más de un centro definido como dl");
        }
        $cDirecciones = $oCentro->getDirecciones();
        if (!is_array($cDirecciones) || empty($cDirecciones)) {
            return _("falta poner la dirección a la dl");
        }
        $poblacion = '';
        $d = 0;
        foreach ($cDirecciones as $oDireccion) {
            $d++;
            if ($d > 1) {
                $poblacion .= '<br>';
            }
            $poblacion .= $oDireccion->getPoblacion();
        }
        return $poblacion;
    }
}
