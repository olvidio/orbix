# Baseline migracion `zonassacd`

## Pantallas incluidas

- `apps/zonassacd/controller/zona_sacd.php` + `zona_sacd_ajax.php`
- `apps/zonassacd/controller/zona_ctr.php` + `zona_ctr_ajax.php`

## Parametros principales

- `que`: `get_lista`, `get_lista_tot`, `update` (segun flujo ajax).
- `id_zona`: puede ser id numerico, `no` o `no_sf` (en centros).
- `id_zona_new`: zona destino o `no`.
- `sel[]`: ids seleccionados.
- `acumular` (solo sacd): `1` cambia asignacion, `2` añade asignacion iglesia/cgi.

## Comportamiento funcional

- `zona_sacd`: lista sacd por zona y permite reasignar; incluye edicion de dias (`dw1..dw7`) via endpoints de `apps/misas`.
- `zona_ctr`: lista centros por zona (dl/sf) y permite reasignar.
- Permisos de escritura condicionados a oficinas `des` o `vcsd`.

## Salida

- Pantallas HTML con formulario y bloque de resultados AJAX.
- Endpoints AJAX devuelven tabla HTML o ejecutan update y devuelven mensaje de error (si aplica).
