<?php

namespace src\ubis\application;

use core\ConfigGlobal;
use function core\is_true;
use function core\urlsafe_b64encode;

final class TelecoTablaData
{
    public static function execute(string $obj_pau, int $id_ubi): array
    {
        $resolver = new TelecoResolver();
        $repoTeleco = $resolver->getTelecoRepo($obj_pau);
        $repoUbi = $resolver->getUbiRepo($obj_pau);

        $coleccion = $repoTeleco->getTelecos(['id_ubi' => $id_ubi]) ?: [];
        $botones = self::getPermisosBotones($obj_pau, $id_ubi, $repoUbi);

        $a_cabeceras = [];
        $a_valores = [];
        $c = 0;
        foreach ($coleccion as $oFila) {
            $v = 0;
            $pks1 = 'get' . ucfirst($oFila->getPrimary_key());
            $val_pks = $oFila->$pks1();
            $pks = urlsafe_b64encode(json_encode($val_pks, JSON_THROW_ON_ERROR));
            $a_valores[$c]['sel'] = $pks;
            foreach ($oFila->getDatosCampos() as $oDatosCampo) {
                if ($c === 0) {
                    $a_cabeceras[] = ucfirst($oDatosCampo->getEtiqueta());
                }
                $v++;
                $metodo = $oDatosCampo->getMetodoGet();
                $valor_camp = (substr($metodo, -2) === 'Vo') ? $oFila->$metodo()->value() : $oFila->$metodo();
                if (!$valor_camp) {
                    $a_valores[$c][$v] = '';
                    continue;
                }
                $var_1 = $oDatosCampo->getArgument();
                $var_2 = $oDatosCampo->getArgument2();
                switch ($oDatosCampo->getTipo()) {
                    case 'fecha':
                        $a_valores[$c][$v] = $valor_camp->getFromLocal();
                        break;
                    case 'array':
                        $lista = $oDatosCampo->getLista();
                        $a_valores[$c][$v] = $lista[$valor_camp];
                        break;
                    case 'depende':
                    case 'opciones':
                        $RepoRelacionado = $GLOBALS['container']->get($var_1);
                        $oRelacionado = $RepoRelacionado->findById($valor_camp);
                        $a_valores[$c][$v] = $oRelacionado?->$var_2() ?: $valor_camp;
                        break;
                    case 'check':
                        $a_valores[$c][$v] = is_true($valor_camp) ? _("sí") : _("no");
                        break;
                    default:
                        $a_valores[$c][$v] = $valor_camp;
                }
            }
            $c++;
        }

        return [
            'botones' => $botones,
            'tit_txt' => _("telecomunicaciones de un centro o casa"),
            'ficha' => 'ficha',
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'a_botones' => ($botones === '1') ? [
                ['txt' => _("modificar"), 'click' => "fnjs_modificar(\"#seleccionados\")"],
                ['txt' => _("eliminar"), 'click' => "fnjs_eliminar(\"#seleccionados\")"],
            ] : [],
        ];
    }

    private static function getPermisosBotones(string $obj_pau, int $id_ubi, object $repoUbi): string
    {
        if (str_contains($obj_pau, 'Dl')) {
            $oUbi = $repoUbi->findById($id_ubi);
            $dl = $oUbi->getDl();
            if ($dl === ConfigGlobal::mi_delef() && $_SESSION['oPerm']->have_perm_oficina('scdl')) {
                return '1';
            }
            return '0';
        }
        if (str_contains($obj_pau, 'Ex') && $_SESSION['oPerm']->have_perm_oficina('scdl')) {
            return '1';
        }
        return '0';
    }
}
