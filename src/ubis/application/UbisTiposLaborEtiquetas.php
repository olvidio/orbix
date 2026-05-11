<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\CuadrosLaborBits;

/**
 * Mapa bit ⇒ etiqueta para tipos de labor (mismo criterio que {@see CuadrosLabor::getTxtTiposLabor()}).
 * Útil cuando un módulo solo necesita los textos sin instanciar permisos/UI en el consumidor.
 */
final class UbisTiposLaborEtiquetas
{
    /**
     * @return array<int, string>
     */
    public static function mapBitToEtiqueta(): array
    {
        return array_flip(CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv()));
    }
}
