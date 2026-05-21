---
tipo: indice
titulo: Mapa legacy Obix → catalogo nuevo
estado: en_construccion
---

# Legacy Obix → catalogo nuevo

Indice de correspondencia entre `documentacion/Documentacion_Obix/mapa_*.md` y la documentacion generada en `docs/catalogo/`.

## actividadtarifas

| mapa legacy | pantalla catalogo | flujo | manual |
|-------------|-------------------|-------|--------|
| `actividadtarifas/mapa_tarifa.md` | `actividadtarifas.pantalla.tarifa` | `tipo_tarifa.gestionar.flujo` | Tipo Tarifa |
| `actividadtarifas/mapa_tarifa_tipo_actividad.md` | `actividadtarifas.pantalla.tarifa_tipo_actividad` | `relacion_tarifa.gestionar.flujo` | Relacion Tarifa |
| `actividadtarifas/mapa_tarifa_ubi.md` | `actividadtarifas.pantalla.tarifa_ubi` | `tarifa_ubi.gestionar.flujo` | Tarifa Ubi |
| `actividadtarifas/mapa_tarifa_actividad.md` | (duplicado menu activ→tarifa) | `tipo_tarifa.gestionar.flujo` | Tipo Tarifa |

Menu CSV: `documentacion/Documentacion_Obix/menus.csv` (filas `actividadtarifas`).

## zonassacd

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `zonassacd/mapa_zona_sacd.md` | `zonassacd.pantalla.zona_sacd` | Zona SACD |
| `zonassacd/mapa_zona_ctr.md` | `zonassacd.pantalla.zona_ctr` | Zona Centros |
| `shared/mapa_tablaDB_lista_ver` + InfoZona | shared | Zonas geogr. (menu) |

## actividadcargos

Sin mapas Obix dedicados. Equivalente funcional:

| Baseline / widget | pantalla catalogo | flujo |
|-------------------|-------------------|-------|
| Select3102 | `select_cargos_de_actividad` | `cargo.gestionar.flujo` |
| Select1302 | `select_cargos_personas_en_actividad` | `cargo.gestionar.flujo` |
| form_3102 | `form_cargos_de_actividad` | `form_cargos_de_actividad.gestionar.flujo` |
| form_1302 | `form_cargos_personas_en_actividad` | `form_cargos_personas_en_actividad.gestionar.flujo` |

Referencia: `documentacion/actividadcargos_migracion_baseline.md`.

## notas

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `notas/mapa_acta_select.md` | `notas.pantalla.acta_select` | Actas |
| `notas/mapa_acta_listado_anual.md` | acta_listado_anual | Acta listado anual |
| `notas/mapa_asignaturas_pendientes.md` | asignaturas_pendientes | Asignaturas pendientes |
| `notas/mapa_asignaturas_pendientes_resumen.md` | asignaturas_pendientes_resumen | Resumen pendientes |
| `notas/mapa_asig_faltan_que.md` | asig_faltan_que | Faltan que… |
| `notas/mapa_resumen_anual.md` | (informes) | Resumen anual |

## usuarios

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `usuarios/mapa_usuario_lista.md` | usuario_lista | Lista usuarios |
| `usuarios/mapa_role_lista.md` | role_lista | Roles |
| `usuarios/mapa_grupo_lista.md` | grupmenu_lista | Grup menu |
| `usuarios/mapa_preferencias.md` | preferencias | Preferencias |
| `usuarios/mapa_borrar_todos_pwd.md` | (admin) | Borrar pwd masivo |

## inventario

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `inventario/mapa_inventario_que.md` | inventario_que | Inventario |
| `inventario/mapa_docs_en_busqueda.md` | docs_en_busqueda | Busqueda docs |
| `inventario/mapa_equipajes_ver.md` | equipajes_ver | Equipajes |
| `inventario/mapa_traslado_doc_que.md` | traslado_doc_que | Traslados |

## menus

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `menus/mapa_menus_que.md` | menus_que | Menus |
| `menus/mapa_grupmenu_lista.md` | grupmenu_lista | Grupos menu |
| `menus/mapa_menus_importar.md` | menus_importar | Importar |
| `menus/mapa_menus_exportar_form.md` | menus_exportar | Exportar |

## encargossacd

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `encargossacd/mapa_sacd_ficha.md` | sacd_ficha | Ficha SACD |
| `encargossacd/mapa_ctr_ficha.md` | ctr_ficha | Ficha centro |
| `encargossacd/mapa_encargo_select.md` | encargo_select | Select encargo |

## actividades (hub)

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `actividades/mapa_actividades_que.md` | actividades_que | Lista actividades |
| `actividades/mapa_actividad_que.md` | actividad_que | Ficha actividad |

## personas (hub)

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `personas/mapa_personas_que.md` | personas_que | Buscar personas |
| `personas/mapa_personas_select.md` | personas_select | Select persona |

## devel / devel_db_admin

| mapa legacy | pantalla catalogo | manual |
|-------------|-------------------|--------|
| `devel/mapa_db_que.md` | db_que | Admin BD |
| `devel/mapa_db_crear_esquema_que.md` | crear_esquema | Crear esquema |
| `devel/mapa_db_eliminar_esquema_que.md` | eliminar_esquema | Eliminar esquema |
| `devel/mapa_db_cambiar_nombre_que.md` | renombrar_esquema | Renombrar esquema |

## Pendiente

Anadir filas al revisar cada modulo: `documentacion/Documentacion_Obix/<modulo>/mapa_*.md` (~120 mapas totales).
