<?php

namespace src\misas\application;

use src\misas\application\support\IdNomJefeResolver;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ModificarEncargosCentrosData
{
    /**
     * Devuelve el desplegable de zonas que el usuario actual puede ver, para
     * pintar la pantalla `modificar_encargos_centros`. Replica la logica de
     * permisos de `apps/misas/controller/modificar_encargos_centros.php`:
     * si el rol es `p-sacd` y NO es jefe de calendario, se limitan las
     * zonas a las del `id_pau` del propio usuario.
     *
     * Devuelve:
     *   - `error`          : texto vacio si todo ok, mensaje si falta permiso.
     *   - `a_opciones_zona`: array id_zona => nombre_zona.
     */
    public static function getData(): array
    {
        $jefe = IdNomJefeResolver::resolve();
        if ($jefe['error'] !== '') {
            return [
                'error' => $jefe['error'],
                'a_opciones_zona' => [],
            ];
        }

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);

        return [
            'error' => '',
            'a_opciones_zona' => $ZonaRepository->getArrayZonas($jefe['id_nom_jefe']),
        ];
    }
}
