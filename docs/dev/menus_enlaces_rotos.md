---
tipo: dev
titulo: Metamenús que apuntan a ficheros inexistentes
fecha: 2026-11-04
estado: pendiente_de_decision
---

# Metamenús que apuntan a ficheros inexistentes

Comprobación automática del 2026-11-03 (dudas D-TAR-2 y D-MNU-1 de
[`MANUAL_REPASO_DUDAS.md`](MANUAL_REPASO_DUDAS.md)). Ocho filas de `aux_metamenus` apuntan a un
controlador que no existe. **No se ha tocado nada**: borrarlas o redirigirlas cambia datos de menús en las
bases de producción, y eso lo decide quien las administra.

## Resumen

Los ocho destinos ya estaban rotos en la versión antigua: en los mapas de `docs/legacy/obix/*/mapa_*.md` el
controlador `apps/...` aparece marcado como error. La migración `apps/` → `frontend/` solo reescribió la
ruta, así que **no hay un sucesor al que redirigir**.

| id | módulo | ruta registrada | texto | ¿sale en un menú visible? |
|----|--------|-----------------|-------|---------------------------|
| 160 | base | `apps/core/mod_tabla_sql.php` | modificar los datos de una tabla | no |
| 141 | activ1 | `frontend/actividades/controller/calendario_que.php` | Calendario que | no |
| — | activ1 | `frontend/actividadtarifas/controller/tarifa_actividad.php` | Tarifa actividades | **sí**: «adl > Tarifas > activ -> tarifa» y «Calendario > Nuevo calendario > Tarifas > definir tarifa» |
| 2 | avisos | `frontend/cambios/controller/avisos_generar_mails.php` | avisos mails | no (en el CSV de semillas apunta a `src/cambios/infrastructure/cli/avisos_generar_mails.php`, que es un script de línea de comandos, no una pantalla) |
| 23 | encargossacd | `frontend/encargossacd/controller/que_tipo_de_encargo.php` | Tipo de encargo | no |
| 53 | inventario | `frontend/inventario/controller/buscar_docs_por_ctr.php` | buscar docs por centro | no |
| 166 | devel | `frontend/menus/controller/pasar_a_demo.php` | pasar a demo | no |
| 38 | base | `frontend/permisos/controller/config_select.php` | config modulos | no |

## Decisiones ya tomadas

- **`tarifa_actividad`** (D-TAR-2, respuesta del 2026-11-04): la pantalla se creará más adelante. Mientras
  tanto la entrada de menú da «File not found»; la guía `administracion/tarifas_de_casas.md` lo indica.

## Pendiente de decidir

Para los otros siete: **dejarlos** (no molestan, no salen en ningún menú) o **eliminarlos** de `aux_metamenus`
y de los CSV de semillas. Si se eliminan, el `DELETE` de cada base sería, por ejemplo:

```sql
-- Revisar antes: comprobar que ninguna fila de aux_menus los usa (id_metamenu).
SELECT id_metamenu, url FROM aux_metamenus WHERE url IN (
  'apps/core/mod_tabla_sql.php',
  'frontend/actividades/controller/calendario_que.php',
  'frontend/cambios/controller/avisos_generar_mails.php',
  'frontend/encargossacd/controller/que_tipo_de_encargo.php',
  'frontend/inventario/controller/buscar_docs_por_ctr.php',
  'frontend/menus/controller/pasar_a_demo.php',
  'frontend/permisos/controller/config_select.php'
);
-- Si no hay referencias en aux_menus: DELETE FROM aux_metamenus WHERE id_metamenu IN (...);
```

Y habría que quitar las mismas filas de `proves/aux_metamenus.csv`.
