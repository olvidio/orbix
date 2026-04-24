<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;
use src\ubis\domain\entity\Ubi;

final class TrasladarUbis
{
    /**
     * @param array<string, mixed> $input
     */
    public static function execute(array $input): string
    {
        $dl_dst = (string)($input['dl_dst'] ?? '');
        $a_sel = $input['sel'] ?? [];
        if (!is_array($a_sel)) {
            $a_sel = [];
        }

        if (empty($a_sel)) {
            return _('No se han seleccionado ubis.');
        }

        $mi_region_dl = ConfigGlobal::mi_region_dl();
        $esquema_org = substr($mi_region_dl, 0, -1);

        $TrasladoUbiRepository = $GLOBALS['container']->get(TrasladoUbiRepositoryInterface::class);

        foreach ($a_sel as $id_ubi) {
            $oUbi = Ubi::NewUbi($id_ubi);
            if ($oUbi === null) {
                continue;
            }

            $classname = self::shortClassName($oUbi);
            switch ($classname) {
                case 'Centro':
                case 'CentroDl':
                    $TrasladoUbiRepository->trasladoCtr((int)$id_ubi, $esquema_org, $dl_dst);
                    break;
                case 'Casa':
                case 'CasaDl':
                    $TrasladoUbiRepository->trasladoCdc((int)$id_ubi, $esquema_org, $dl_dst);
                    break;
            }
        }

        return '';
    }

    private static function shortClassName(object $o): string
    {
        $c = get_class($o);
        $p = strrpos($c, '\\');

        return $p === false ? $c : substr($c, $p + 1);
    }
}
