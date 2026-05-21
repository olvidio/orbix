---
tipo: manual_usuario
modulo: devel_db_admin
flujos: 15
estado_revision: revisado_parcial
---

# Manual De Usuario - devel_db_admin

Herramientas **administrador BD** (solo entornos dev/admin): esquemas, migraciones, apptables.

## Acceso

Menu desarrollo / admin — controllers en `frontend/devel_db_admin/controller/` (db_que, crear/eliminar esquema, mover tabla, migraciones…).

## Operaciones Tipicas

| Operacion | Endpoint |
|-----------|----------|
| Crear / eliminar esquema | `crear_esquema`, `eliminar_esquema` |
| Copiar / absorber / renombrar | `copiar_esquema`, `absorber_esquema`, `renombrar_esquema`, `verificar_*`, `corregir_*` |
| Migraciones | `migraciones_lista_data`, `migraciones_ejecutar`, `migraciones_quitar_registro` |
| Mover tabla | `mover_tabla` |
| Apptables | `apptables_apps_data`, `db_propiedades_data` |

## Aviso

No usar en produccion sin protocolo. Coordinar con cambios en carpeta `db/` del repo.

Generador API corregido para `function (): void` en routes.php (2026-05-21).
