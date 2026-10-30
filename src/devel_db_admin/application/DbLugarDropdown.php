<?php

declare(strict_types=1);

namespace src\devel_db_admin\application;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\criteria\DelegacionField;

/**
 * Datos del desplegable de delegación (dl) filtradas por región, para pantallas de administración de BD.
 */
final class DbLugarDropdown
{
    /**
     * Payload JSON estándar de desplegable (contrato refactor.md).
     *
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool}
     */
    public static function getData(string $region, DelegacionRepositoryInterface $repoDl): array
    {
        return [
            'id' => 'dl',
            'opciones' => $region === '' ? [] : OpcionesDesplegable::enOrden(self::opcionesPorRegion($region, $repoDl)),
            'selected' => '',
            'blanco' => false,
        ];
    }

    /**
     * @return array<string, string> valor => etiqueta
     */
    public static function opcionesPorRegion(string $region, DelegacionRepositoryInterface $repoDl): array
    {
        $criterio = Criteria::create()->where(Filter::eq(DelegacionField::Active, true));
        if ($region !== '') {
            $criterio = $criterio->andWhere(Filter::eq(DelegacionField::Region, $region));
        }
        $cDelegaciones = $repoDl->matching($criterio->orderBy(DelegacionField::Dl));
        $aOpciones = [];
        foreach ($cDelegaciones as $oDeleg) {
            $dlValue = $oDeleg->getDlVo()->value();
            if (!is_string($dlValue) || $dlValue === '') {
                continue;
            }
            $dl = $dlValue;
            $aOpciones[$dl] = $dl;
        }
        if ($region !== '') {
            $aOpciones[$region] = (string) _("para gestión global");
        }

        return $aOpciones;
    }
}
