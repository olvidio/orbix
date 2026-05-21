---
tipo: manual_usuario
modulo: actividades
flujos: 28
estado_revision: revisado_parcial
---

# Manual De Usuario - actividades

Modulo **hub**: busqueda, alta, edicion, calendarios y tipos de actividad. Casi todos los menus de actividades (roles 2, 3, 8, 10, 20…) apuntan aqui.

## Acceso Por Menu (patrones)

| Accion | Controller | Parametros tipicos |
|--------|------------|-------------------|
| **Buscar** crt/ca/cv/cve | `actividad_select.php` | `sactividad`, `sasistentes`, `que=ver` |
| **Importar** | `actividad_que.php` | `modo=importar` |
| **List varios** | `actividad_que.php` | `que=list_cjto` |
| **Calendario casas** | `calendario_listas.php` | `que=c_comunes`, `c_comunes_sf/sv` |
| **Tipo actividad** | `tipo_activ.php` | Catalogo tipos |
| **Nuevo curso** | `actividad_nuevo_curso.php` | Duplicar actividades curso |

Parametros `sactividad`: `crt`, `ca`, `cv`, `cve`, etc. definen subconjunto y permisos.

## Buscar Y Abrir Actividades

1. Menu **Buscar …** segun rol (crt, ca, agd…).
2. Filtros en `actividad_select` / `actividad_que_filtros`.
3. Listado → abrir ficha (`actividad_ver`) con dossiers (asistentes, cargos, plazas, procesos…).

## Crear, Editar, Eliminar

- Formularios JS: `_actividad_form.js`, calendario → `/src/actividades/actividad_nuevo`, `actividad_editar`.
- **Eliminar** desde listados (`actividad_select`, `lista_actividades_sg`) → `actividad_eliminar`.
- **Duplicar**, **importar**, **cambiar tipo**, **publicar** — acciones en ficha o listado (endpoints API homonimos).

## Calendario Y Listas Por Casa

- **Casas comunes** (roles 8, 20): `calendario_listas.php` — variantes sf/sv/comunes.
- Enlace a **casas**, **actividadtarifas**, **resumen plazas** desde JS actividades.

## Tipos De Actividad

- Mantenimiento catalogo `tipo_activ` — metadata, formularios modificar.
- Afecta permisos en asistentes, cargos, SACD, procesos.

## Modulos Dependientes (documentados aparte)

actividadplazas, actividadcargos, asistentes, actividadtarifas, actividadessacd, actividadescentro, procesos, planning, pasarela, notas, casas…

Legacy: mapas `documentacion/Documentacion_Obix/actividades/mapa_*.md`
