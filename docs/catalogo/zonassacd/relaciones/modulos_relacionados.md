---
tipo: relaciones_modulos
modulo: zonassacd
estado_revision: revisado_parcial
---

# Modulos relacionados — zonassacd

## Dependencias

| Modulo | Uso |
|--------|-----|
| personas | Repositorio `PersonaSacd` en listados |
| misas | `zona_sacd_datos_get` / `zona_sacd_datos_put` — dias semana SACD en modal |
| shared | Mantenimiento tablas `InfoZona` via `tablaDB_lista_ver` |
| permisos | Escritura: `des`, `vcsd` |

## Dependientes

| Modulo | Uso |
|--------|-----|
| encargossacd | Zonas geograficas en contexto SACD |
| ubis / casas | Centros por zona (`zona_ctr`) |

## Endpoints en migracion

Rutas registradas sin controller en `infrastructure/ui/http/controllers/` (revisar al cerrar migracion):

- `/src/zonassacd/zona_sacd_ajax` — legacy dispatcher `que=get_lista|update|...`
- `/src/zonassacd/zona_ctr_ajax`

Sustitutos actuales en frontend:

- `zona_sacd_lista_ajax.php` → `/src/zonassacd/zona_sacd_lista`
- `zona_sacd_update_ajax.php` → `/src/zonassacd/zona_sacd_update`
- `zona_ctr_lista_ajax.php` → `/src/zonassacd/zona_ctr_lista`
- `zona_ctr_update_ajax.php` → `/src/zonassacd/zona_ctr_update`

## Legacy

- `docs/dev/zonassacd_migracion_baseline.md`
- Obix: `docs/legacy/obix/zonassacd/mapa_*.md`
