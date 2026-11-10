<?php

declare(strict_types=1);

namespace Tests\unit\permisos\domain\support;

use ReflectionProperty;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XResto;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Permisos de actividades sin SQL: el constructor real carga `aux_usuarios_perm`; aquí se
 * siembra la matriz a mano y se inyectan repositorios simulados cuando el caso los necesita.
 */
final class PermisosActividadesConMatriz extends PermisosActividades
{
    public function __construct(bool $propia = true)
    {
        $this->idUsuario = 1;
        $this->bpropia = $propia;
    }

    /**
     * @param array<string, XResto> $dl     permisos para actividades de la dl propia
     * @param array<string, XResto> $otras  permisos para el resto de delegaciones
     */
    public function conMatriz(array $dl, array $otras = []): self
    {
        $this->aPermDl = $dl;
        $this->aPermOtras = $otras;

        return $this;
    }

    public function conRepositorios(
        ?ActividadAllRepositoryInterface $actividad = null,
        ?ActividadProcesoTareaRepositoryInterface $procesoTarea = null,
        ?TipoDeActividadRepositoryInterface $tipoDeActividad = null,
        ?TareaProcesoRepositoryInterface $tareaProceso = null,
    ): self {
        $repos = [
            'actividadAllRepository' => $actividad,
            'actividadProcesoTareaRepository' => $procesoTarea,
            'tipoDeActividadRepository' => $tipoDeActividad,
            'tareaProcesoRepository' => $tareaProceso,
        ];
        foreach ($repos as $propiedad => $repo) {
            if ($repo === null) {
                continue;
            }
            $ref = new ReflectionProperty(PermisosActividades::class, $propiedad);
            $ref->setValue($this, $repo);
        }

        return $this;
    }

    /**
     * Estado interno legible (el mismo que se guarda en sesión).
     *
     * @return array<string, mixed>
     */
    public function estado(): array
    {
        return $this->__serialize();
    }

    /**
     * Regla de permisos de un tipo: [afecta, fase, perm_on, perm_off] por elemento.
     *
     * @param list<array{0: int, 1: int|string, 2: int, 3: int}> $reglas
     */
    public static function resto(string $tipo, array $reglas): XResto
    {
        $x = new XResto($tipo);
        foreach ($reglas as [$afecta, $fase, $on, $off]) {
            $x->setOmplir($afecta, $fase, $on, $off);
        }
        $x->setOrdenar();

        return $x;
    }
}
