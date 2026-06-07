<?php

namespace src\ubis\application;

use src\shared\domain\DatosCampo;
use src\ubis\application\services\UbiPermisos;
use src\ubis\application\services\UbiRepositoryResolver;
use src\ubis\domain\entity\TelecoUbi;
use function src\shared\domain\helpers\is_true;
use function src\shared\domain\helpers\urlsafe_b64encode;

final class TelecoTablaData
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function execute(string $obj_pau, int $id_ubi): array
    {
        $repoTeleco = $this->ubiRepositoryResolver->getTelecoRepository($obj_pau);
        $repoUbi = $this->ubiRepositoryResolver->getRepository($obj_pau);

        /** @var list<TelecoUbi> $coleccion */
        $coleccion = $repoTeleco->getTelecos(['id_ubi' => $id_ubi]) ?: [];
        $botones = $this->getPermisosBotones($obj_pau, $id_ubi, $repoUbi);

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
                if (!$oDatosCampo instanceof DatosCampo) {
                    continue;
                }
                if ($c === 0) {
                    $a_cabeceras[] = ucfirst($oDatosCampo->getEtiqueta());
                }
                $v++;
                $metodoRaw = $oDatosCampo->getMetodoGet();
                if (!is_string($metodoRaw) || $metodoRaw === '') {
                    continue;
                }
                $metodo = $metodoRaw;
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
                        $oRelacionado = null;
                        if ($var_1 !== '' && class_exists($var_1)) {
                            $repoRelacionado = $this->ubiRepositoryResolver->getDireccionRepositoryByInterface($var_1);
                            $oRelacionado = $repoRelacionado->findById((int) $valor_camp);
                        }
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
        $oUbi = null;
        if (str_contains($obj_pau, 'Dl') && method_exists($repoUbi, 'findById')) {
            $oUbi = $repoUbi->findById($id_ubi);
        }
        return UbiPermisos::puedeModificar($obj_pau, $oUbi) ? '1' : '0';
    }
}
