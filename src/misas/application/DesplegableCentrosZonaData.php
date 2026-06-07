<?php

namespace src\misas\application;

use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;

class DesplegableCentrosZonaData
{

    public function __construct(
        private readonly CentroEllasRepositoryInterface $centroEllasRepository,
        private readonly CentroEllosRepositoryInterface $centroEllosRepository,
    ) {
    }
    /**
     * Payload JSON para el desplegable de centros activos de una zona.
     *
     * Orden: sf (alfabetico), linea separadora, sv (alfabetico).
     *
     * @param int      $id_zona    Zona de la que sacar los centros.
     * @param int|null $id_ubi_sel Centro preseleccionado (opcional).
     * @return array<string, mixed>
     */
    public function getData(int $id_zona, ?int $id_ubi_sel = null): array
    {
        $opciones_sf = [];
        $opciones_sv = [];

        if ($id_zona !== 0) {
            $aWhere = [
                'active' => 't',
                'id_zona' => $id_zona,
                '_ordre' => 'nombre_ubi',
            ];

            $opciones_sf = self::mapaCentrosOrdenados($this->centroEllasRepository->getCentros($aWhere));
            $opciones_sv = self::mapaCentrosOrdenados($this->centroEllosRepository->getCentros($aWhere));
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
     * @param list<CentroEllas|CentroEllos> $cCentros
     * @return array<string, string>
     */
    private static function mapaCentrosOrdenados(array $cCentros): array
    {
        $opciones = [];
        foreach ($cCentros as $oCentro) {
            $opciones[(string) $oCentro->getId_ubi()] = $oCentro->getNombre_ubi();
        }
        asort($opciones, SORT_LOCALE_STRING);

        return array_combine(
            array_map(static fn (int|string $key): string => (string) $key, array_keys($opciones)),
            array_values($opciones),
        ) ?: [];
    }
}
