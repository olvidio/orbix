<?php

namespace src\ubis\application\services;

use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use web\Desplegable;

/**
 * Helper para construir desplegables (select) de TipoCasa
 *
 * - Ordenadas por nombre_region
 * - value = region (sigla), label = nombre_region
 */
final class TipoCentroDropdown
{
    /**
     *
     * @param string $nombreCampo Nombre del campo select (por defecto 'region')
     * @param bool $conBlanco Si debe incluir opción en blanco (por defecto true)
     */
    public static function listaTiposCentro(bool $conBlanco, string $nombreCampo): Desplegable
    {
        $repo = $GLOBALS['container']->get(TipoCentroRepositoryInterface::class);
        $opciones = $repo->getArrayTiposCentro();

        $despl = new Desplegable();
        $despl->setNombre($nombreCampo);
        if ($conBlanco) {
            $despl->setBlanco(true);
        }
        $despl->setOpciones($opciones);

        return $despl;
    }
}
