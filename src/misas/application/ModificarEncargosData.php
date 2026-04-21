<?php

namespace src\misas\application;

use src\misas\application\support\IdNomJefeResolver;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class ModificarEncargosData
{
    /**
     * Devuelve los datos para pintar la pantalla `modificar_encargos`:
     * el desplegable de zonas (filtrado segun el rol del usuario) y la lista
     * de criterios de orden aceptados por el grid.
     *
     * Replica la logica de `apps/misas/controller/modificar_encargos.php`:
     * si el rol es `p-sacd` y NO es jefe de calendario, se limitan las
     * zonas a las del `id_pau` del propio usuario.
     *
     * Devuelve:
     *   - `error`          : texto vacio si todo ok, mensaje si el usuario
     *                         no tiene permiso para ver la pantalla.
     *   - `a_opciones_zona`: array id_zona => nombre_zona.
     *   - `a_orden`        : array criterio => label.
     */
    public static function getData(): array
    {
        $jefe = IdNomJefeResolver::resolve();
        if ($jefe['error'] !== '') {
            return [
                'error' => $jefe['error'],
                'a_opciones_zona' => [],
                'a_orden' => [],
            ];
        }

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $a_opciones_zona = $ZonaRepository->getArrayZonas($jefe['id_nom_jefe']);

        $a_orden = [
            'orden' => _('orden'),
            'prioridad' => _('prioridad'),
            'desc_enc' => _('alfabético'),
        ];

        return [
            'error' => '',
            'a_opciones_zona' => $a_opciones_zona,
            'a_orden' => $a_orden,
        ];
    }
}
