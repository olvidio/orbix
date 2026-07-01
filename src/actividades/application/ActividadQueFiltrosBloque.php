<?php

namespace src\actividades\application;

use src\actividades\application\ActividadLugar;
use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\ubis\application\services\DelegacionDropdown;
use src\usuarios\domain\entity\Role;

/**
 * Datos del bloque "filtros extra" (filtro_lugar + lugar + organiza + publicada)
 * en la pantalla `actividad_que`. El bloque solo se muestra a usuarios con
 * permiso de control (`perm_ctr`); para el resto devuelve `visible: false`.
 *
 * Los desplegables se devuelven como payloads JSON estándar; el frontend
 * construye el HTML (ver `actividad_que.html.twig`).
 */
final class ActividadQueFiltrosBloque
{
    public function __construct(
        private ActividadLugar $actividadLugar,
        private DelegacionDropdown $delegacionDropdown,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function ejecutar(
        int $sfsv,
        string $modo,
        string $dl_org,
        string $filtro_lugar,
        int $id_ubi,
        int $publicado,
        bool $proceso_installed
    ): array {
        if (!$this->tienePermisoControl()) {
            return ['visible' => false];
        }

        $mi_dele = ConfigGlobal::mi_delef((string) $sfsv);

        $filtroLugarSelect = [
            'id' => 'filtro_lugar',
            'opciones' => OpcionesDesplegable::enOrden($this->delegacionDropdown->dlURegionesFiltro($sfsv)),
            'selected' => (string) $filtro_lugar,
            'action' => 'fnjs_lugar()',
            'blanco' => true,
        ];

        $lugarSelect = null;
        if ($filtro_lugar !== '') {
            $lugarSelect = [
                'id' => 'id_ubi',
                'opciones' => OpcionesDesplegable::enOrden($this->actividadLugar->getLugaresPosibles($filtro_lugar)),
                'selected' => $id_ubi > 0 ? (string) $id_ubi : '',
                'blanco' => true,
            ];
        }

        $dlOrgOpciones = $this->delegacionDropdown->delegacionesURegiones($sfsv, true);
        $dlOrgSelect = [
            'id' => 'dl_org',
            'opciones' => OpcionesDesplegable::enOrden($dlOrgOpciones),
            'selected' => (string) $dl_org,
            'blanco' => true,
            'action' => '',
            'opcion_no' => [],
        ];
        if ($modo === 'importar') {
            $dlOrgSelect['opcion_no'] = [$mi_dele];
        }
        if ($modo === 'publicar') {
            $dlOrgSelect['opciones'] = OpcionesDesplegable::enOrden([$mi_dele => $mi_dele]);
            $dlOrgSelect['blanco'] = false;
        }
        if ($proceso_installed) {
            $dlOrgSelect['action'] = 'fnjs_actualizar_fases();';
        }

        return [
            'visible' => true,
            'filtro_lugar' => $filtroLugarSelect,
            'lugar' => $lugarSelect,
            'dl_org' => $dlOrgSelect,
            'publicado' => [
                'show' => $modo !== 'importar',
                'value' => $publicado,
            ],
            'labels' => [
                'lugar_pais_dl' => (string) _('lugar según país o dl'),
                'lugar' => (string) _('lugar'),
                'organiza' => (string) _('organiza'),
                'publicada' => (string) _('publicada'),
                'si' => (string) _('si'),
                'no' => (string) _('no'),
                'todas' => (string) _('todas'),
            ],
        ];
    }

    private function tienePermisoControl(): bool
    {
        $oUsuario = ConfigGlobal::MiUsuario();
        $oRole = new Role();
        $oRole->setId_role($oUsuario?->getId_role() ?? 0);
        return !$oRole->isRolePau('ctr');
    }
}
