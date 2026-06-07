<?php

namespace src\permisos\domain;

class XResto
{
    /**
     * Campos legacy conservados para deserializar sesiones PHP creadas antes del
     * cierre PHPStan (2026-06); el runtime actual solo usa $iid_tipo_activ y $aDades.
     */
    protected int|string $iid_activ = 0;

    protected string $iid_tipo_activ;

    /** @var list<int|string> */
    protected array $aFases = [];

    protected int $iaccion = 0;

    /** @var array<int|string, mixed>|null */
    protected ?array $aAfecta = null;

    protected int $iGenerador = 0;

    /**
     * @var array<int, array<int|string, array<string, int>>>
     */
    protected array $aDades = [];

    public function __construct(string $iid_tipo_activ)
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    public function setOmplir(int $iAfecta, int|string $fase_ref, int $perm_on, int $perm_off): void
    {
        $this->aDades[$iAfecta][$fase_ref]['on'] = $perm_on;
        $this->aDades[$iAfecta][$fase_ref]['off'] = $perm_off;
    }

    public function hasAfecta(int $iAfecta): bool
    {
        foreach ($this->aDades as $sumaAfecta => $arr) {
            $has_one = (($sumaAfecta & $iAfecta) != 0);
            if ($has_one) {
                return true;
            }
        }

        return false;
    }

    public function getFaseRef(int $iAfecta): int|string|null
    {
        if (!isset($this->aDades[$iAfecta])) {
            return null;
        }
        $fase_ref = key($this->aDades[$iAfecta]);

        return is_int($fase_ref) || is_string($fase_ref) ? $fase_ref : null;
    }

    public function getPerm(int $iAfecta, int|string $id_fase_ref, string $on_off): int
    {
        if (empty($this->aDades[$iAfecta][$id_fase_ref][$on_off])) {
            return 0;
        }

        return (int) $this->aDades[$iAfecta][$id_fase_ref][$on_off];
    }

    public function setOrdenar(): void
    {
        ksort($this->aDades);
    }

    /**
     * @return list<int|string>
     */
    public function getFases(): array
    {
        $aFases = [];
        foreach ($this->aDades as $byProceso) {
            foreach ($byProceso as $id_fase => $perm) {
                $aFases[] = $id_fase;
            }
        }

        return $aFases;
    }
}
