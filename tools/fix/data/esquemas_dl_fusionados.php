<?php

declare(strict_types=1);

/**
 * Mapa de esquemas DL absorbidos / fusionados → esquema matriz actual.
 *
 * Claves y valores: nombre base `region-dl` **sin** sufijo `v`/`f`
 * (p. ej. `H-dlz` → `H-dlal`). El script aplica el sufijo sfsv.
 *
 * Completar con el resto de fusiones históricas.
 * Debe coincidir con el mapa embebido en:
 *   db/migrations/202607211300_repatriar_notas_otra_region_a_acta__{sv,sf}.sql
 *
 * @return array<string, string>
 */
return [
    // Actas/notas de estas DL viven ahora en dlal
    'H-dlz' => 'H-dlal',
    'H-dlv' => 'H-dlal',
    // Absorbidas por dln
    'H-dlva' => 'H-dln', // prefijo de acta «dlva …»
    'H-dlst' => 'H-dln',
];

