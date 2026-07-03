<?php

namespace src\ubis\application;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\TrasladoUbiRepositoryInterface;
use src\ubis\domain\entity\Ubi;

final class TrasladarUbis
{
    public function __construct(
        private TrasladoUbiRepositoryInterface $rasladoUbiRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $dl_dst = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'dl_dst');
        $a_sel = $input['sel'] ?? [];
        if (!is_array($a_sel)) {
            $a_sel = [];
        }

        if (empty($a_sel)) {
            return _('No se han seleccionado ubis.');
        }

        $mi_region_dl = ConfigGlobal::mi_region_dl();
        $esquema_org = substr($mi_region_dl, 0, -1);

        $TrasladoUbiRepository = $this->rasladoUbiRepository;

        foreach ($a_sel as $id_ubi) {
            if (!is_int($id_ubi) && !is_string($id_ubi)) {
                continue;
            }
            $oUbi = Ubi::NewUbi($id_ubi);
            if ($oUbi === null) {
                continue;
            }
            $idUbiInt = is_int($id_ubi) ? $id_ubi : (int) $id_ubi;

            $classname = self::shortClassName($oUbi);
            switch ($classname) {
                case 'Centro':
                case 'CentroDl':
                    $TrasladoUbiRepository->trasladoCtr($idUbiInt, $esquema_org, $dl_dst);
                    break;
                case 'Casa':
                case 'CasaDl':
                    $TrasladoUbiRepository->trasladoCdc($idUbiInt, $esquema_org, $dl_dst);
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
