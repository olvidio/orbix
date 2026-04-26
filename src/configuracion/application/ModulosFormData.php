<?php

namespace src\configuracion\application;

use frontend\shared\web\Posicion;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\ModulosConfig;
use web\Hash;

/**
 * Formulario de módulo (`frontend/configuracion/controller/modulos_form.php`).
 */
final class ModulosFormData
{
    private const POSICION_SCRIPT = '/frontend/configuracion/controller/modulos_form.php';

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        $Qmod = (string)($input['mod'] ?? '');

        $Qid_mod = 0;
        $nom = '';
        $descripcion = '';
        $a_mods_req = [];
        $a_apps_req = [];

        if ($Qmod !== 'nuevo') {
            $a_sel = isset($input['sel']) ? (array)$input['sel'] : [];
            if ($a_sel !== []) {
                $Qid_mod = (int)strtok((string)($a_sel[0] ?? ''), '#');
            } else {
                $Qid_mod = (int)($input['id_mod'] ?? 0);
            }

            if (isset($input['stack']) && (string)$input['stack'] !== '') {
                $stack = (string)filter_var($input['stack'], FILTER_SANITIZE_NUMBER_INT);
                if ($stack !== '') {
                    $oPosicion2 = new Posicion(self::POSICION_SCRIPT, $input);
                    if ($oPosicion2->goStack($stack)) {
                        $oPosicion2->olvidar($stack);
                    }
                }
            }

            $ModuloRepository = $GLOBALS['container']->get(ModuloRepositoryInterface::class);
            $oModulo = $ModuloRepository->findById($Qid_mod);
            if (!empty($oModulo)) {
                $nom = $oModulo->getNomVo()->value();
                $descripcion = $oModulo->getDescripcionVo()?->value() ?? '';
                $a_mods_req = $oModulo->getModsReqVo()?->toArray() ?? [];
                $a_apps_req = $oModulo->getAppsReqVo()?->toArray() ?? [];
            }
        }

        $oModulosConfig = new ModulosConfig();

        $a_mods_todos = $oModulosConfig->getModsAll();
        $a_apps_todas = $oModulosConfig->getAppsAll();

        $a_apps_mod = [];
        if (count($a_mods_req) > 0) {
            $all = [];
            foreach ($a_mods_req as $id_mod_req) {
                $all[] = $oModulosConfig->getAppsMods($id_mod_req);
            }
            $a_apps_mod = array_merge(...$all);
            $a_apps_mod = array_unique($a_apps_mod);
        }

        $campos_chk = 'sel_mods!sel_apps';
        $camposForm = 'nom!descripcion!';

        $oHash = new Hash();
        $oHash->setCamposForm($camposForm);
        $oHash->setcamposNo($campos_chk);
        $oHash->setArraycamposHidden([
            'campos_chk' => $campos_chk,
            'id_mod' => $Qid_mod,
            'mod' => $Qmod,
        ]);

        $oHashActualizar = new Hash();
        $oHashActualizar->setCamposNo('refresh');
        $oHashActualizar->setArraycamposHidden([
            'id_mod' => $Qid_mod,
        ]);

        return [
            'hash_form_html' => $oHash->getCamposHtml(),
            'hash_actualizar_html' => $oHashActualizar->getCamposHtml(),
            'id_mod' => $Qid_mod,
            'nom' => $nom,
            'descripcion' => $descripcion,
            'a_mods_todos' => $a_mods_todos,
            'a_apps_todas' => $a_apps_todas,
            'a_mods_req' => $a_mods_req,
            'a_apps_req' => $a_apps_req,
            'a_apps_mod' => $a_apps_mod,
        ];
    }
}
