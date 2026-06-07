<?php

namespace src\misas\application;

use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

class VerInicialesZonaData
{

    public function __construct(
        private readonly ZonaSacdRepositoryInterface $zonaSacdRepository,
        private readonly PersonaSacdRepositoryInterface $personaSacdRepository,
        private readonly InicialesSacdRepositoryInterface $inicialesSacdRepository,
    ) {
    }
    /**
     * @return array<string, mixed>
     */
    public function getData(int $id_zona): array
    {
        $columns = [
            ['id' => 'nombre_sacd', 'name' => 'Nombre sacd', 'field' => 'nombre_sacd', 'width' => 220, 'cssClass' => 'cell-title'],
            ['id' => 'iniciales', 'name' => 'Iniciales', 'field' => 'iniciales', 'width' => 110, 'cssClass' => 'cell-title'],
            ['id' => 'color', 'name' => 'Color', 'field' => 'color', 'width' => 64, 'cssClass' => 'cell-title'],
        ];

        $a_Id_nom = $this->zonaSacdRepository->getIdSacdsDeZona($id_zona);

        $rows = [];
        foreach ($a_Id_nom as $id_nom) {
            $PersonaSacd = $this->personaSacdRepository->findById($id_nom);
            if ($PersonaSacd === null) {
                $sacd = '?';
                $iniciales = '';
                $color = '';
            } else {
                $sacd = $PersonaSacd->getNombreApellidos();
                $InicialesSacd = $this->inicialesSacdRepository->findById($id_nom);
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
