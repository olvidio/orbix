<?php

declare(strict_types=1);

namespace src\notas\domain\contracts;

/**
 * Mapa permanente `public.mapa_prefijo_acta_esquema`:
 * prefijo del nº de acta → esquema base sin sufijo v/f.
 *
 * Fuente única de verdad para:
 * - repatriar `otra_region` / routing de notas con acta histórica;
 * - saber qué prefijos de acta «pertenecen» a una DL (incl. absorbidas);
 * - registrar fusiones al absorber esquemas.
 */
interface MapaPrefijoActaEsquemaRepositoryInterface
{
    /**
     * @return string|null Esquema base (p. ej. `H-dlb`, `M-crM`) o null si no hay fila
     */
    public function esquemaBasePorPrefijo(string $prefijo): ?string;

    /**
     * Primera palabra del campo acta → esquema físico (`base` + `v`/`f`).
     *
     * @param string $acta Valor completo de `acta` (p. ej. `dlb 12/24`)
     * @param string $suffix `v` o `f`
     */
    public function esquemaDestinoDesdeActa(string $acta, string $suffix): ?string;

    /**
     * Prefijos de acta cuya DL examinadora es este esquema base
     * (p. ej. `H-dlal` → `dlal`, `dlz`, `dlv`).
     *
     * @return list<string>
     */
    public function prefijosPorEsquemaBase(string $esquemaBase): array;

    /**
     * ¿El prefijo del acta pertenece a este esquema base (mapa)?
     */
    public function prefijoPerteneceAEsquema(string $prefijo, string $esquemaBase): bool;

    /**
     * Fusiones de esquemas derivadas del mapa (`notas` con «fusion»).
     * Claves/valores: base sin v/f (p. ej. `H-dlz` → `H-dlal`).
     *
     * @return array<string, string>
     */
    public function fusionesEsquemaBase(): array;

    /**
     * Alta / actualización de un prefijo (p. ej. tras AbsorberEsquema).
     */
    public function upsertPrefijo(string $prefijo, string $esquemaBase, ?string $notas = null): void;

    /**
     * Reasigna todos los prefijos que apuntaban al esquema absorbido.
     */
    public function reasignarEsquemaBase(string $esquemaBaseAnterior, string $esquemaBaseNuevo): void;
}
