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
 * aunque queden huecos por el medio. 9998 y 9999 son marcas de fin de
 * ciclo, no opcionales.
 */
final class HuecoOpcionalDePlan
{
    public const UMBRAL_OPCIONAL = 3000;
    /** Por encima de esto ya no es una opcional concreta (marcas 9998/9999). */
    public const MAX_OPCIONAL = 9000;
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
     * Primer hueco del plan que no ocupa otra nota. Si esta asignatura ya
     * está en un hueco válido, se conserva. Un hueco intermedio libre
     * (2430 y 2432 ocupados, 2431 no) se usa antes que el siguiente.
     *
     * Cualquier nota cuyo `id_nivel` sea un slot lo ocupa, también si no
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
            if (isset($slotSet[$nivel])) {
                $ocupados[$nivel] = true;
            }
        }

        if ($propio !== null && isset($slotSet[$propio]) && !isset($ocupados[$propio])) {
            return $propio;
        }

        foreach ($slots as $slot) {
            $slot = (int) $slot;
            if (!isset($ocupados[$slot])) {
                return $slot;
            }
        }

        return null;
    }
}
