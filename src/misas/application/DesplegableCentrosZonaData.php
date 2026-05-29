<?php

namespace src\misas\application;

use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;

class DesplegableCentrosZonaData
{
    /**
     * Payload JSON para el desplegable de centros activos de una zona.
     *
     * Orden: sf (alfabetico), linea separadora, sv (alfabetico).
     *
     * @param int      $id_zona    Zona de la que sacar los centros.
     * @param int|null $id_ubi_sel Centro preseleccionado (opcional).
     */
    public static function getData(int $id_zona, ?int $id_ubi_sel = null): array
    {
        $opciones_sf = [];
        $opciones_sv = [];

        if ($id_zona !== 0) {
            $aWhere = [
                'active' => 't',
                'id_zona' => $id_zona,
                '_ordre' => 'nombre_ubi',
            ];
            $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            $CentroEllosRepository = $GLOBALS['container']->get(CentroEllosRepositoryInterface::class);

            $opciones_sf = self::mapaCentrosOrdenados($CentroEllasRepository->getCentros($aWhere));
            $opciones_sv = self::mapaCentrosOrdenados($CentroEllosRepository->getCentros($aWhere));
        }

        return [
            'id' => 'id_ubi',
            'opciones_sf' => $opciones_sf,
            'opciones_sv' => $opciones_sv,
            'selected' => $id_ubi_sel !== null && $id_ubi_sel !== 0 ? (string)$id_ubi_sel : '',
            'blanco' => true,
            'val_blanco' => '',
            'action' => '',
        ];
    }

    /**
     * @param array<int, object>|bool $cCentros
     * @return array<string, string>
     */
    private static function mapaCentrosOrdenados(array|bool $cCentros): array
    {
        if (!is_array($cCentros)) {
            return [];
        }

        $opciones = [];
        foreach ($cCentros as $oCentro) {
            $opciones[(string)$oCentro->getId_ubi()] = (string)$oCentro->getNombre_ubi();
        }
        asort($opciones, SORT_LOCALE_STRING);

        return $opciones;
    }
}
