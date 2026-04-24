<?php

namespace src\cambios\application;

use core\ConfigGlobal;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use function core\is_true;

/**
 * Mutacion: crea o actualiza un `CambioUsuarioObjetoPref` (la parte de la
 * preferencia que describe "que objeto/tipo de actividad/fase/aviso" se
 * escucha). Sucesor de la rama `guardar_objeto` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */
final class CambioUsuarioObjetoPrefGuardar
{
    private const PAD_MAP = [1 => '.', 2 => '..', 3 => '...', 4 => '....', 5 => '.....'];

    /**
     * @param array $input
     * @return array{error: string, id_item_usuario_objeto: int}
     */
    public static function execute(array $input): array
    {
        $id_item_usuario_objeto = (int)($input['id_item_usuario_objeto'] ?? 0);
        $id_usuario = (int)($input['id_usuario'] ?? 0);
        $id_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $dl_propia = is_true($input['dl_propia'] ?? '');
        $objeto = (string)($input['objeto'] ?? '');
        $aviso_tipo = (int)($input['aviso_tipo'] ?? 0);
        $id_fase_ref = (int)($input['id_fase_ref'] ?? 0);
        $aviso_off = is_true($input['aviso_off'] ?? '');
        $aviso_on = is_true($input['aviso_on'] ?? '');
        $aviso_outdate = is_true($input['aviso_outdate'] ?? '');
        $a_casas = (array)($input['casas'] ?? []);

        if ($id_usuario <= 0) {
            return [
                'error' => (string)_("falta id_usuario"),
                'id_item_usuario_objeto' => 0,
            ];
        }

        $Repo = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        if ($id_item_usuario_objeto > 0) {
            $oPref = $Repo->findById($id_item_usuario_objeto);
            if ($oPref === null) {
                $oPref = new CambioUsuarioObjetoPref();
                $oPref->setId_item_usuario_objeto($id_item_usuario_objeto);
            }
        } else {
            $newId = $Repo->getNewId();
            $oPref = new CambioUsuarioObjetoPref();
            $oPref->setId_item_usuario_objeto($newId);
        }

        $oPref->setId_usuario($id_usuario);

        if ($dl_propia) {
            $isfsv = (int)substr($id_tipo_activ, 0, 1);
            $dl_org = ConfigGlobal::mi_delef($isfsv);
        } else {
            $dl_org = 'x';
        }
        $oPref->setDl_org($dl_org);

        $len = strlen($id_tipo_activ);
        if ($len !== 6) {
            $dif = 6 - $len;
            if ($dif > 0 && isset(self::PAD_MAP[$dif])) {
                $id_tipo_activ .= self::PAD_MAP[$dif];
            } else {
                return [
                    'error' => (string)_("id_tipo_activ invalido"),
                    'id_item_usuario_objeto' => 0,
                ];
            }
        }
        $oPref->setId_tipo_activ_txt($id_tipo_activ);
        $oPref->setObjeto($objeto);
        $oPref->setAviso_tipo($aviso_tipo);
        $oPref->setId_fase_ref($id_fase_ref);
        $oPref->setAviso_off($aviso_off);
        $oPref->setAviso_on($aviso_on);
        $oPref->setAviso_outdate($aviso_outdate);

        if (!empty($a_casas)) {
            $a_casas = array_filter(array_map('strval', $a_casas), static fn($v) => $v !== '' && $v !== '0');
            if (!empty($a_casas)) {
                $oPref->setCsv_id_pau(implode(',', $a_casas));
            }
        }

        if ($Repo->Guardar($oPref) === false) {
            return [
                'error' => (string)_("Hay un error, no se ha guardado"),
                'id_item_usuario_objeto' => 0,
            ];
        }

        return [
            'error' => '',
            'id_item_usuario_objeto' => $oPref->getId_item_usuario_objeto(),
        ];
    }
}
