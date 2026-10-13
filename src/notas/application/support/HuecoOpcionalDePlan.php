<?php

declare(strict_types=1);

namespace src\notas\application\support;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\support\PlanEstudiosFilter;
use src\notas\application\PlanEstudiosDePersona;

/**
 * Hueco curricular de una asignatura opcional concreta.
 *
 * Los slots no son consecutivos (plan 2026: 2430–2434; plan 1997 añade
 * 1230–1232). Se toma el primero libre del catálogo del plan del alumno,
 * aunque queden huecos por el medio. Si el catálogo está lleno, el
 * siguiente es 2435, 2436… (opcional de sobra): el plan 2026 no vuelve
 * a 1230–1232. 9998 y 9999 son marcas de fin de ciclo, no opcionales.
 */
final class HuecoOpcionalDePlan
{
    public const UMBRAL_OPCIONAL = 3000;
    /** Por encima de esto ya no es una opcional concreta (marcas 9998/9999). */
    public const MAX_OPCIONAL = 9000;
    /** Primera id_nivel de opcional que ya no está en el catálogo. */
    public const SOBRA_DESDE = 2435;
    public const SOBRA_HASTA = 2499;
    public const ID_FIN_CUADRIENIO = 9998;
    public const ID_FIN_BIENIO = 9999;
    /** `xa_asignaturas.id_tipo` de las opcionales genéricas (Op. I, bienio…). */
    public const ID_TIPO_OPCIONAL = 8;

    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly PlanEstudiosDePersona $planEstudiosDePersona,
    ) {
    }

    public static function esMarcadorFinCiclo(int $idAsignatura): bool
    {
        return $idAsignatura === self::ID_FIN_CUADRIENIO
            || $idAsignatura === self::ID_FIN_BIENIO;
    }

    public static function esOpcionalConcreta(int $idAsignatura): bool
    {
        return $idAsignatura > self::UMBRAL_OPCIONAL
            && $idAsignatura < self::MAX_OPCIONAL;
    }

    /**
     * Niveles genéricos del plan (`id_tipo` 8, `id_nivel` < 3000), en orden.
     *
     * @return list<int>
     */
    public function nivelesDelPlan(int $plan): array
    {
        [$where, $operador] = PlanEstudiosFilter::apply($plan, [
            'active' => 't',
            'id_tipo' => self::ID_TIPO_OPCIONAL,
            'id_nivel' => self::UMBRAL_OPCIONAL,
            'id_asignatura' => self::UMBRAL_OPCIONAL,
            '_ordre' => 'id_nivel',
        ], [
            'id_nivel' => '<',
            'id_asignatura' => '<',
        ]);

        $slots = [];
        foreach ($this->asignaturaRepository->getAsignaturas($where, $operador) as $asignatura) {
            if (!$asignatura->isActive() || (int) $asignatura->getId_tipo() !== self::ID_TIPO_OPCIONAL) {
                continue;
            }
            $nivel = (int) $asignatura->getId_nivel();
            $idAsignatura = (int) $asignatura->getId_asignatura();
            if ($nivel < 1 || $nivel >= self::UMBRAL_OPCIONAL || $idAsignatura >= self::UMBRAL_OPCIONAL) {
                continue;
            }
            $slots[$nivel] = $nivel;
        }
        ksort($slots);

        return array_values($slots);
    }

    /**
     * @param list<array{id_asignatura: int, id_nivel: int}> $notas
     */
    public function idNivelPara(int $idNom, int $idAsignatura, array $notas): ?int
    {
        $plan = $this->planEstudiosDePersona->resolve($idNom);

        return self::resolver($idAsignatura, $this->nivelesDelPlan($plan), $notas);
    }

    /**
     * @param list<array{id_asignatura: int, id_nivel: int}> $notas
     */
    public function nivelAlGuardar(int $idNom, int $idAsignatura, int $posted, array $notas): ?int
    {
        $plan = $this->planEstudiosDePersona->resolve($idNom);

        return self::aceptarOReubicar($posted, $idAsignatura, $this->nivelesDelPlan($plan), $notas);
    }

    /**
     * Alta: conserva el `id_nivel` publicado si es un hueco libre del plan.
     * 1230–1232 no lo son en el plan 2026: se reubica al primer libre
     * (2430–2434 y, si no cabe, 2435+).
     *
     * @param list<int> $slots
     * @param list<array{id_asignatura: int, id_nivel: int}> $notas
     */
    public static function aceptarOReubicar(int $posted, int $idAsignatura, array $slots, array $notas): ?int
    {
        $slotSet = [];
        foreach ($slots as $slot) {
            $slotSet[(int) $slot] = true;
        }

        $ocupado = false;
        foreach ($notas as $nota) {
            if ((int) $nota['id_asignatura'] === $idAsignatura) {
                continue;
            }
            if ((int) $nota['id_nivel'] === $posted) {
                $ocupado = true;
                break;
            }
        }

        if ($posted > 0 && isset($slotSet[$posted]) && !$ocupado) {
            return $posted;
        }

        return self::resolver($idAsignatura, $slots, $notas);
    }

    /**
     * Siguiente id_nivel de sobra (2435+) cuando el catálogo del plan ya
     * no tiene hueco. Null si aún cabe en un slot del plan.
     *
     * @param list<int> $slots
     * @param list<array{id_asignatura: int, id_nivel: int}> $notas
     */
    public static function siguienteSobra(array $slots, array $notas): ?int
    {
        $siguiente = self::resolver(0, $slots, $notas);
        if ($siguiente !== null && $siguiente >= self::SOBRA_DESDE) {
            return $siguiente;
        }

        return null;
    }

    /**
     * Primer hueco del plan que no ocupa otra nota. Si esta asignatura ya
     * está en un hueco válido, se conserva. Un hueco intermedio libre
     * (2430 y 2432 ocupados, 2431 no) se usa antes que el siguiente.
     * Con el catálogo lleno sigue en 2435, 2436… hasta 2499.
     *
     * Cualquier nota cuyo `id_nivel` coincida lo ocupa, también si no
     * está aprobada: la PK es `(id_nom, id_nivel, tipo_acta)`.
     *
     * @param list<int> $slots
     * @param list<array{id_asignatura: int, id_nivel: int}> $notas
     */
    public static function resolver(int $idAsignatura, array $slots, array $notas): ?int
    {
        $slotSet = [];
        foreach ($slots as $slot) {
            $slotSet[(int) $slot] = true;
        }

        $propio = null;
        $ocupados = [];
        foreach ($notas as $nota) {
            $nivel = (int) $nota['id_nivel'];
            if ((int) $nota['id_asignatura'] === $idAsignatura) {
                $propio = $nivel;
                continue;
            }
            if ($nivel > 0) {
                $ocupados[$nivel] = true;
            }
        }

        if (
            $propio !== null
            && !isset($ocupados[$propio])
            && (isset($slotSet[$propio]) || self::esSobra($propio))
        ) {
            return $propio;
        }

        foreach ($slots as $slot) {
            $slot = (int) $slot;
            if (!isset($ocupados[$slot])) {
                return $slot;
            }
        }

        for ($nivel = self::SOBRA_DESDE; $nivel <= self::SOBRA_HASTA; $nivel++) {
            if (!isset($ocupados[$nivel])) {
                return $nivel;
            }
        }

        return null;
    }

    private static function esSobra(int $nivel): bool
    {
        return $nivel >= self::SOBRA_DESDE && $nivel <= self::SOBRA_HASTA;
    }
}
