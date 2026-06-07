<?php

namespace src\configuracion\application;

use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\strtoupper_dlb;

/**
 * Listado de módulos (`frontend/configuracion/controller/modulos_select.php`).
 *
 * Hash del listado: {@see \frontend\configuracion\helpers\ModulosSelectRender}.
 */
final class ModulosSelectData
{
    public function __construct(
        private ModuloRepositoryInterface $moduloRepository,
        private AppRepositoryInterface $appRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        require_once dirname(__DIR__, 2) . '/shared/domain/helpers/func_tablas.php';

        $Qid_sel = input_string($input, 'id_sel');
        $Qscroll_id = input_string($input, 'scroll_id');

        if (input_string($input, 'stack') !== '') {
            $stack = filter_var(input_string($input, 'stack'), FILTER_SANITIZE_NUMBER_INT);
            if ($stack !== false && $stack !== '0' && $stack !== '') {
                if (array_key_exists('restored_id_sel', $input)) {
                    $Qid_sel = input_string($input, 'restored_id_sel');
                }
                if (array_key_exists('restored_scroll_id', $input)) {
                    $Qscroll_id = input_string($input, 'restored_scroll_id');
                }
            }
        }

        $aWhere = ['_ordre' => 'nom'];
        $aOperador = [];
        $cModulos = $this->moduloRepository->getModulos($aWhere, $aOperador);

        $a_botones = [
            ['txt' => _("modificar"), 'click' => 'fnjs_modificar("#seleccionados")'],
            ['txt' => _("eliminar"), 'click' => 'fnjs_eliminar("#seleccionados")'],
        ];

        $a_cabeceras = [
            ucfirst(_("nombre")),
            ucfirst(_("descripción")),
            _("módulos requeridos"),
            _("aplicaciones requeridas"),
        ];

        $cMods = $this->moduloRepository->getModulos();
        $a_mods_todos = [];
        foreach ($cMods as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $oMod->getNom();
            $a_mods_todos[$id_mod] = $nom_mod;
        }

        $cApps = $this->appRepository->getApps();
        $a_apps_todas = [];
        foreach ($cApps as $oApp) {
            $id_app = $oApp->getIdAppVo()->value();
            $nom_app = $oApp->getNomVo()->value();
            $a_apps_todas[$id_app] = $nom_app;
        }

        $i = 0;
        $a_valores = [];
        foreach ($cModulos as $oModulo) {
            $i++;
            $id_mod = $oModulo->getId_mod();
            $nom = $oModulo->getNom();
            $descripcion = $oModulo->getDescripcion();
            $mods_req = $oModulo->getMods_req();
            $apps_req = $oModulo->getApps_req();

            $lista_mods = '';
            if (!empty($mods_req)) {
                foreach ($mods_req as $mod) {
                    if (empty($mod)) {
                        continue;
                    }
                    $lista_mods .= $lista_mods === '' ? '' : ', ';
                    $lista_mods .= $a_mods_todos[$mod] ?? (string)$mod;
                }
            }

            $lista_apps = '';
            if (!empty($apps_req)) {
                foreach ($apps_req as $app) {
                    if (empty($app)) {
                        continue;
                    }
                    $lista_apps .= $lista_apps === '' ? '' : ', ';
                    $lista_apps .= $a_apps_todas[$app] ?? (string)$app;
                }
            }

            $a_valores[$i]['sel'] = "$id_mod#";
            $a_valores[$i][1] = $nom;
            $a_valores[$i][2] = $descripcion;
            $a_valores[$i][3] = $lista_mods;
            $a_valores[$i][4] = $lista_apps;
        }

        if ($Qid_sel !== '') {
            $a_valores['select'] = $Qid_sel;
        }
        if ($Qscroll_id !== '') {
            $a_valores['scroll_id'] = $Qscroll_id;
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
            'hash_lista' => [
                'campos_form' => 'sel!mod',
                'campos_no' => 'scroll_id!sel!refresh',
            ],
            'txt_eliminar' => _("¿Está seguro?"),
            'txt_anadir_modulo' => strtoupper_dlb(_("añadir módulo")),
        ];
    }
}
