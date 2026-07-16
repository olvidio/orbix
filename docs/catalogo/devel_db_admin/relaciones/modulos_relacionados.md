---
tipo: relaciones_modulos
modulo: devel_db_admin
estado_revision: revisado_parcial
---

# Modulos relacionados — devel_db_admin

## Rol

Administracion de esquemas, migraciones y apptables (**solo dev/admin**).

## Relacion

| Modulo | Uso |
|--------|-----|
| dbextern | Sync externo tras cambios de esquema |
| configuracion | Propiedades BD y apps |
| utils_database | Utilidades CLI complementarias (sin HTTP) |

## Documentacion cruzada

- Manual: `docs/manual/devel_db_admin.md`
- Legacy: `docs/legacy/obix/devel/mapa_db_*.md`
- Fix generador 2026-05-21: closures `function (): void` en routes.php
