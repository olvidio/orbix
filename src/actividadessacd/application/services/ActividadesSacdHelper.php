<?php

namespace src\actividadessacd\application\services;

use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Helpers para la comunicacion a los sacd.
 */
final class ActividadesSacdHelper
{
    /** @var array<string, array<string, string>> */
    private array $a_txt = [];

    public function __construct(
        private ActividadSacdTextoRepositoryInterface $actividadSacdTextoRepository,
        private CentroDlRepositoryInterface $centroDlRepository,
    ) {
    }

    /**
     * @return array<string, string>|false
     */
    public function getArrayTraducciones(string $idioma): array|false
    {
        $idioma = $idioma === '' ? 'es' : $idioma;
        if (empty($this->a_txt[$idioma])) {
            $cTextos = $this->actividadSacdTextoRepository->getActividadSacdTextos([]);
            foreach ($cTextos as $oTexto) {
                $clave = $oTexto->getClave();
                $lang = $oTexto->getIdioma();
                $this->a_txt[$lang][$clave] = (string)($oTexto->getTexto() ?? '');
            }
        }
        if (empty($this->a_txt[$idioma])) {
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
        $a_traduccion = $this->getArrayTraducciones('es');
        if (is_array($a_traduccion) && !empty($a_traduccion[$clave])) {
            return $a_traduccion[$clave];
        }
        return sprintf(_("falta definir el texto %s en este idioma: %s"), $clave, $idioma);
    }

    public function getLugar_dl(): string
    {
        if (ConfigGlobal::is_dmz()) {
            return 'xxxx';
        }
        $cCentros = $this->centroDlRepository->getCentros(['tipo_ctr' => 'dl']);
        $num_dl = count($cCentros);
        $oCentro = null;
        switch ($num_dl) {
            case 0:
                $cCentros = $this->centroDlRepository->getCentros(['tipo_ctr' => 'cr']);
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
                return _("Más de un centro definido como dl");
        }
        $cDirecciones = $oCentro->getDirecciones();
        if (count($cDirecciones) === 0) {
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
