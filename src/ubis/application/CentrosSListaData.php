<?php

namespace src\ubis\application;

use src\ubis\domain\criteria\CentroDlField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Listado de centros de tipo 's' (sacerdotes) con el número de personas s asignadas
 * en cada uno, y el total global.
 */
final class CentrosSListaData
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private PersonaSRepositoryInterface $personaSRepository,
    ) {
    }

    /**
     * @return array{a_cabeceras: list<string>, a_valores: array<int, array<int, int|string>>, num_total_s: int}
     */
    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $CentroRepository = $this->centroDlRepository;
        $cCentros = $CentroRepository->matching(
            Criteria::create()
                ->where(Filter::matches(F::TipoCtr, '^s[^s]'))
                ->andWhere(Filter::eq(F::Active, true))
                ->orderBy(F::NombreUbi)
        );

        $PersonaSRepository = $this->personaSRepository;
        $num_total_s = 0;
        $a_valores = [];
        $i = 0;
        foreach ($cCentros as $oCentro) {
            $i++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $cPersonasCtr = $PersonaSRepository->matching(Criteria::create()
                ->where(Filter::eq(PersonaField::IdCtr, $id_ubi))
                ->andWhere(Filter::eq(PersonaField::Situacion, 'A')));
            $num_s = count($cPersonasCtr);
            $num_total_s += $num_s;

            $a_valores[$i][1] = $nombre_ubi;
            $a_valores[$i][2] = $num_s;
        }

        $a_cabeceras = [ucfirst(_("centro")), _("num s")];

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'num_total_s' => $num_total_s,
        ];
    }
}
