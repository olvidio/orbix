<?php

namespace src\encargossacd\application;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;
use src\ubis\domain\criteria\CentroEllasField;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEllas;

/**
 * Centros activos filtrados por grupo (propuestas / listados por encargo).
 */
final class PropuestasCentrosPorFiltro
{
    public function __construct(
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
    ) {
    }

    /**
     * @return list<CentroDl|CentroEllas>
     */
    public function execute(int $filtro_ctr, bool $todosEnDefault = false): array
    {
        return match ($filtro_ctr) {
            1 => $this->dl('^a|n|s[^s]|of'),
            2 => $this->centroEllasRepository->matching(
                Criteria::create()
                    ->where(Filter::eq(CentroEllasField::Active, true))
                    ->orderBy(CentroEllasField::NombreUbi)
            ),
            3 => $this->dl('^ss'),
            4 => $this->dl('igl'),
            5 => array_merge(
                $this->dl('cgioc|oc|cgi'),
                $this->centroEllasRepository->matching(
                    Criteria::create()
                        ->where(Filter::eq(CentroEllasField::Active, true))
                        ->andWhere(Filter::matches(CentroEllasField::TipoCtr, 'cgioc|oc|cgi'))
                        ->orderBy(CentroEllasField::NombreUbi)
                ),
            ),
            default => $todosEnDefault ? array_merge(
                $this->centroDlRepository->matching(
                    Criteria::create()
                        ->where(Filter::eq(CentroDlField::Active, true))
                        ->orderBy(CentroDlField::TipoCtr)
                        ->addOrderBy(CentroDlField::NombreUbi)
                ),
                $this->centroEllasRepository->matching(
                    Criteria::create()
                        ->where(Filter::eq(CentroEllasField::Active, true))
                        ->orderBy(CentroEllasField::TipoCtr)
                        ->addOrderBy(CentroEllasField::NombreUbi)
                ),
            ) : [],
        };
    }

    /**
     * Centros de la delegación activos cuyo `tipo_ctr` cumple la expresión regular.
     *
     * @return list<CentroDl>
     */
    private function dl(string $patronTipoCtr): array
    {
        return $this->centroDlRepository->matching(
            Criteria::create()
                ->where(Filter::eq(CentroDlField::Active, true))
                ->andWhere(Filter::matches(CentroDlField::TipoCtr, $patronTipoCtr))
                ->orderBy(CentroDlField::NombreUbi)
        );
    }
}
