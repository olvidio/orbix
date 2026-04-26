<?php

namespace src\configuracion\application;

use frontend\shared\web\Posicion;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use function src\shared\domain\helpers\strtoupper_dlb;
use web\Hash;

/**
 * Listado de módulos (`frontend/configuracion/controller/modulos_select.php`).
 */
final class ModulosSelectData
{
    private const POSICION_SCRIPT = '/frontend/configuracion/controller/modulos_select.php';

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        require_once dirname(__DIR__, 2) . '/shared/domain/helpers/func_tablas.php';

        $Qid_sel = (string)($input['id_sel'] ?? '');
        $Qscroll_id = (string)($input['scroll_id'] ?? '');

        if (isset($input['stack']) && (string)$input['stack'] !== '') {
            $stack = (string)filter_var($input['stack'], FILTER_SANITIZE_NUMBER_INT);
            if ($stack !== '') {
                $oPosicion2 = new Posicion(self::POSICION_SCRIPT, $input);
                if ($oPosicion2->goStack($stack)) {
                    $Qid_sel = (string)$oPosicion2->getParametro('id_sel');
                    $Qscroll_id = (string)$oPosicion2->getParametro('scroll_id');
                    $oPosicion2->olvidar($stack);
                }
            }
        }

        $aWhere = ['_ordre' => 'nom'];
        $aOperador = [];
        $ModuloRepository = $GLOBALS['container']->get(ModuloRepositoryInterface::class);
        $cModulos = $ModuloRepository->getModulos($aWhere, $aOperador);

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

        $cMods = $ModuloRepository->getModulos();
        $a_mods_todos = [];
        foreach ($cMods as $oMod) {
            $id_mod = $oMod->getId_mod();
            $nom_mod = $oMod->getNom();
            $a_mods_todos[$id_mod] = $nom_mod;
        }

        $AppRepository = $GLOBALS['container']->get(AppRepositoryInterface::class);
        $cApps = $AppRepository->getApps();
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

        $oHash = new Hash();
        $oHash->setCamposForm('sel!mod');
        $oHash->setcamposNo('scroll_id!sel!refresh');

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
            'hash_lista_html' => $oHash->getCamposHtml(),
            'txt_eliminar' => _("¿Está seguro?"),
            'txt_anadir_modulo' => strtoupper_dlb(_("añadir módulo")),
        ];
    }
}
