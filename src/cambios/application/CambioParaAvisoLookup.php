<?php

namespace src\cambios\application;

use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\criteria\CambioField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\cambios\domain\entity\Cambio;

/**
 * Resuelve un cambio referenciado desde av_cambios_usuario.
 *
 * Los cambios de la propia dl viven en av_cambios_dl; los de otras dl (importadas
 * aquí) se sincronizan en ONLY public.av_cambios con id_schema del origen.
 */
final class CambioParaAvisoLookup
{
    public function __construct(
        private CambioRepositoryInterface $cambioRepository,
        private CambioDlRepositoryInterface $cambioDlRepository,
    ) {
    }

    public function find(int $id_schema_cambio, int $id_item_cambio): ?Cambio
    {
        $criteria = Criteria::create()
            ->where(Filter::eq(CambioField::IdSchema, $id_schema_cambio))
            ->andWhere(Filter::eq(CambioField::IdItemCambio, $id_item_cambio));

        $cCambios = $this->cambioDlRepository->matching($criteria);
        if ($cCambios !== []) {
            return $cCambios[0];
        }

        $cCambios = $this->cambioRepository->matching($criteria);
        if ($cCambios !== []) {
            return $cCambios[0];
        }

        return null;
    }
}
