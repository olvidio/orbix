<?php

namespace src\ubis\application;

use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

class ListaCtrsData
{
    /**
     * @return array{a_cabeceras: list<string>, a_valores: array<int, array<int, int|string>>, num_total_s: int}
     */
    public static function execute(): array
    {
        $CentroRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $CentroRepository->getCentros(
            ['tipo_ctr' => '^s[^s]', 'active' => 't', '_ordre' => 'nombre_ubi'],
            ['tipo_ctr' => '~']
        );

        $PersonaSRepository = $GLOBALS['container']->get(PersonaSRepositoryInterface::class);
        $num_total_s = 0;
        $a_valores = [];
        $i = 0;
        foreach ($cCentros as $oCentro) {
            $i++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $cPersonasCtr = $PersonaSRepository->getPersonas(['id_ctr' => $id_ubi, 'situacion' => 'A']);
            if ($cPersonasCtr === false) {
                return [
                    'error' => _('error'),
                    'a_cabeceras' => [],
                    'a_valores' => [],
                    'num_total_s' => 0,
                ];
            }
            $num_s = count($cPersonasCtr);
            $num_total_s += $num_s;

            $a_valores[$i][1] = $nombre_ubi;
            $a_valores[$i][2] = $num_s;
        }

        $a_cabeceras = [ucfirst(_('centro')), _('num s')];

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'num_total_s' => $num_total_s,
        ];
    }
}
