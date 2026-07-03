<?php

namespace src\configuracion\application;

use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\ModulosConfig;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Formulario de módulo (`frontend/configuracion/controller/modulos_form.php`).
 *
 * Campos hash del formulario: {@see \frontend\configuracion\helpers\ModulosFormRender}.
 */
final class ModulosFormData
{
    public function __construct(
        private ModuloRepositoryInterface $moduloRepository,
        private ModulosConfig $modulosConfig,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $Qmod = FuncTablasSupport::inputString($input, 'mod');

        $Qid_mod = 0;
        $nom = '';
        $descripcion = '';
        $a_mods_req = [];
        $a_apps_req = [];

        if ($Qmod !== 'nuevo') {
            $a_sel = isset($input['sel']) && is_array($input['sel']) ? $input['sel'] : [];
            if ($a_sel !== []) {
                $first = $a_sel[0] ?? '';
                $selString = is_scalar($first) ? (string)$first : '';
                $Qid_mod = (int)strtok($selString, '#');
            } else {
                $Qid_mod = FuncTablasSupport::inputInt($input, 'id_mod');
            }

            $oModulo = $this->moduloRepository->findById($Qid_mod);
            if ($oModulo !== null) {
                $nom = $oModulo->getNomVo()->value();
                $descripcion = $oModulo->getDescripcionVo()?->value() ?? '';
                $a_mods_req = $oModulo->getModsReqVo()?->toArray() ?? [];
                $a_apps_req = $oModulo->getAppsReqVo()?->toArray() ?? [];
            }
        }

        $a_mods_todos = $this->modulosConfig->getModsAll();
        $a_apps_todas = $this->modulosConfig->getAppsAll();

        $a_apps_mod = [];
        if ($a_mods_req !== []) {
            $all = [];
            foreach ($a_mods_req as $id_mod_req) {
                $all[] = $this->modulosConfig->getAppsMods((int)$id_mod_req);
            }
            $a_apps_mod = array_merge(...$all);
            $a_apps_mod = array_values(array_unique($a_apps_mod));
        }

        $campos_chk = 'sel_mods!sel_apps';

        return [
            'hash_main' => [
                'campos_form' => 'nom!descripcion!',
                'campos_no' => $campos_chk,
                'campos_hidden' => [
                    'campos_chk' => $campos_chk,
                    'id_mod' => $Qid_mod,
                    'mod' => $Qmod,
                ],
            ],
            'hash_actualizar' => [
                'campos_no' => 'refresh',
                'campos_hidden' => [
                    'id_mod' => $Qid_mod,
                ],
            ],
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
