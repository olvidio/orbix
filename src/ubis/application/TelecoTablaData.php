<?php

namespace src\ubis\application;

use src\shared\infrastructure\ProvidesRepositories;
use src\ubis\application\services\UbiPermisos;
use function src\shared\domain\helpers\is_true;
use function src\shared\domain\helpers\urlsafe_b64encode;

final class TelecoTablaData
{
    use ProvidesRepositories;

    public static function execute(string $obj_pau, int $id_ubi): array
    {
        return (new self())->run($obj_pau, $id_ubi);
    }

    private function run(string $obj_pau, int $id_ubi): array
    {
        $repoTeleco = $this->getTelecoRepository($obj_pau);
        $repoUbi = $this->getRepository($obj_pau);

        $coleccion = $repoTeleco->getTelecos(['id_ubi' => $id_ubi]) ?: [];
        $botones = $this->getPermisosBotones($obj_pau, $id_ubi, $repoUbi);

        $a_cabeceras = [];
        $a_valores = [];
        $c = 0;
        foreach ($coleccion as $oFila) {
            $v = 0;
            $pks1 = 'get' . ucfirst($oFila->getPrimary_key() ?? '');
            $val_pks = $oFila->$pks1();
            $pks = urlsafe_b64encode(json_encode($val_pks, JSON_THROW_ON_ERROR));
            $a_valores[$c]['sel'] = $pks;
            foreach ($oFila->getDatosCampos() as $oDatosCampo) {
                if ($c === 0) {
                    $a_cabeceras[] = ucfirst($oDatosCampo->getEtiqueta() ?? '');
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
                        if (substr($var_2, -2) === 'Vo') {
                            $a_valores[$c][$v] = $oRelacionado?->$var_2()?->value() ?: $valor_camp;
                        } else {
                            $a_valores[$c][$v] = $oRelacionado?->$var_2() ?: $valor_camp;
                        }
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

    private function getPermisosBotones(string $obj_pau, int $id_ubi, object $repoUbi): string
    {
        $oUbi = str_contains($obj_pau, 'Dl') ? $repoUbi->findById($id_ubi) : null;
        return UbiPermisos::puedeModificar($obj_pau, $oUbi) ? '1' : '0';
    }
}
