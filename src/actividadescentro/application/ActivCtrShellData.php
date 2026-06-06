<?php

namespace src\actividadescentro\application;

use src\shared\config\ConfigGlobal;
use function src\shared\domain\helpers\input_string;

/**
 * Tipo resuelto y especificaciones de URL para la shell de `activ_ctr` (sin `HashFront` en `src/`).
 * La firma `linkSinVal` se aplica en {@see frontend\actividadescentro\controller\activ_ctr}.
 */
final class ActivCtrShellData
{
    /**
     * @param array{tipo?: string, year?: string, periodo?: string} $in
     *
     * @return array{
     *     tipo: string,
     *     url_lista: array{path: string, campos_form: string},
     *     url_encargados: array{path: string, campos_form: string},
     *     url_disponibles: array{path: string, campos_form: string},
     *     url_asignar: array{path: string, campos_form: string},
     *     url_reordenar: array{path: string, campos_form: string},
     *     url_eliminar: array{path: string, campos_form: string}
     * }
     */
    public function build(array $in): array
    {
        $Qtipo = input_string($in, 'tipo');
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

        return [
            'tipo' => $Qtipo,
            'url_lista' => [
                'path' => 'src/actividadescentro/lista_actividades_ctr_data',
                'campos_form' => 'tipo!year!periodo!empiezamin!empiezamax',
            ],
            'url_encargados' => [
                'path' => 'src/actividadescentro/centros_encargados_data',
                'campos_form' => 'id_activ!id_tipo_activ!dl_org',
            ],
            'url_disponibles' => [
                'path' => 'src/actividadescentro/centros_disponibles_data',
                'campos_form' => 'tipo!id_activ!inicio!fin!f_ini_act',
            ],
            'url_asignar' => [
                'path' => 'src/actividadescentro/centro_encargado_asignar',
                'campos_form' => 'id_activ!id_ubi',
            ],
            'url_reordenar' => [
                'path' => 'src/actividadescentro/centro_encargado_reordenar',
                'campos_form' => 'id_activ!id_ubi!num_orden',
            ],
            'url_eliminar' => [
                'path' => 'src/actividadescentro/centro_encargado_eliminar',
                'campos_form' => 'id_activ!id_ubi',
            ],
        ];
    }
}
