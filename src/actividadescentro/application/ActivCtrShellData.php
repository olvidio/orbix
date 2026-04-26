<?php

namespace src\actividadescentro\application;

use src\shared\config\ConfigGlobal;
use web\Hash;

/**
 * URLs firmadas y tipo resuelto para la shell de `activ_ctr` (sin ConfigGlobal en el front).
 */
class ActivCtrShellData
{
    /**
     * @param array{tipo?: string, year?: string, periodo?: string} $in
     * @return array{tipo: string, url_lista: string, url_encargados: string, url_disponibles: string, url_asignar: string, url_reordenar: string, url_eliminar: string}
     */
    public static function build(array $in): array
    {
        $Qtipo = (string)($in['tipo'] ?? '');
        if (ConfigGlobal::mi_sfsv() === 2) {
            switch ($Qtipo) {
                case 'sg':
                    $Qtipo = 'sfsg';
                    break;
                case 'sr':
                    $Qtipo = 'sfsr';
                    break;
                case 'nagd':
                    $Qtipo = 'sfnagd';
                    break;
            }
        }

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $buildHashedUrl = static function (string $url, string $campos): string {
            $oHash = new Hash();
            $oHash->setUrl($url);
            $oHash->setCamposForm($campos);
            return $url . $oHash->linkSinVal();
        };

        return [
            'tipo' => $Qtipo,
            'url_lista' => $buildHashedUrl(
                $web . '/src/actividadescentro/lista_actividades_ctr_data',
                'tipo!year!periodo!empiezamin!empiezamax'
            ),
            'url_encargados' => $buildHashedUrl(
                $web . '/src/actividadescentro/centros_encargados_data',
                'id_activ!id_tipo_activ!dl_org'
            ),
            'url_disponibles' => $buildHashedUrl(
                $web . '/src/actividadescentro/centros_disponibles_data',
                'tipo!id_activ!inicio!fin!f_ini_act'
            ),
            'url_asignar' => $buildHashedUrl(
                $web . '/src/actividadescentro/centro_encargado_asignar',
                'id_activ!id_ubi'
            ),
            'url_reordenar' => $buildHashedUrl(
                $web . '/src/actividadescentro/centro_encargado_reordenar',
                'id_activ!id_ubi!num_orden'
            ),
            'url_eliminar' => $buildHashedUrl(
                $web . '/src/actividadescentro/centro_encargado_eliminar',
                'id_activ!id_ubi'
            ),
        ];
    }
}
