<?php

namespace src\misas\application;

use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

class VerInicialesZonaData
{
    public static function getData(int $id_zona): array
    {
        $columns = [
            ['id' => 'nombre_sacd', 'name' => 'Nombre sacd', 'field' => 'nombre_sacd', 'width' => 220, 'cssClass' => 'cell-title'],
            ['id' => 'iniciales', 'name' => 'Iniciales', 'field' => 'iniciales', 'width' => 110, 'cssClass' => 'cell-title'],
            ['id' => 'color', 'name' => 'Color', 'field' => 'color', 'width' => 64, 'cssClass' => 'cell-title'],
        ];

        $ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
        $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
        $InicialesSacdRepository = $GLOBALS['container']->get(InicialesSacdRepositoryInterface::class);

        $a_Id_nom = $ZonaSacdRepository->getIdSacdsDeZona($id_zona);

        $rows = [];
        foreach ($a_Id_nom as $id_nom) {
            $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
            if ($PersonaSacd === null) {
                $sacd = '?';
                $iniciales = '';
                $color = '';
            } else {
                $sacd = $PersonaSacd->getNombreApellidos();
                $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
                if ($InicialesSacd === null) {
                    $iniciales = '';
                    $color = '';
                } else {
                    $iniciales = $InicialesSacd->getIniciales() ?? '';
                    $color = $InicialesSacd->getColor() ?? '';
                }
            }
            $rows[] = [
                'id_sacd' => (int)$id_nom,
                'nombre_sacd' => $sacd,
                'iniciales' => $iniciales,
                'color' => InicialesColorHex::normalizeForStorage($color),
            ];
        }

        return [
            'id_zona' => $id_zona,
            'columns' => $columns,
            'rows' => $rows,
        ];
    }
}
