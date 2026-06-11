---
id: "actividades.pantalla.actividad_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Buscar actividad (filtros)"
controller: "frontend/actividades/controller/actividad_que.php"
vistas: ["frontend/actividades/view/actividad_que.html.twig"]
fragmentos_frontend: ["frontend/actividades/controller/actividad_select.php", "frontend/actividades/controller/lista_activ.php", "frontend/asistentes/controller/lista_asis_conjunto_activ.php"]
endpoints: ["/src/actividades/actividad_que_datos", "/src/actividades/actividad_que_filtros", "/src/actividades/actividad_tipo_get", "/src/procesos/actividad_que_fases_ajax"]
capacidades: ["actividades.actividad_que.gestionar", "actividades.actividad_que_filtros.gestionar", "actividades.actividad_tipo.gestionar"]
campos: ["post.modo", "post.que", "post.status", "post.id_tipo_activ", "post.filtro_lugar", "post.id_ubi", "post.nom_activ", "post.periodo", "post.year", "post.dl_org", "post.empiezamin", "post.empiezamax", "post.fases_on", "post.fases_off", "post.listar_asistentes", "post.publicado", "post.sasistentes", "post.sactividad", "post.sactividad2", "post.snom_tipo", "post.extendida", "post.stack"]
acciones: ["fnjs_buscar", "fnjs_cargar_filtros_extra", "fnjs_actualizar_fases", "fnjs_lugar", "fnjs_asistentes", "fnjs_actividad", "fnjs_nom_tipo", "fnjs_id_activ", "fnjs_comprobar_fase_no_duplicadas"]
estado_revision: "revisado"
---

# Buscar actividad (filtros)

Pantalla de **filtros de busqueda de actividades**. Es la entrada de los menus
*Buscar*, *Importar* (`modo=importar`), *Publicar* (`modo=publicar`) y de los
listados conjuntos (`que=list_cjto…`). El usuario concreta tipo de actividad
(cascada sf/sv → asistentes → actividad → tipo), estado, nombre, lugar,
organiza, publicada, periodo y (con `procesos`) fases marcadas/sin marcar; el
boton **buscar** envia el formulario a la pantalla de resultados.

Destino del formulario segun `que`:

| `que` | Destino |
|-------|---------|
| `list_activ`, `list_activ_compl` | `frontend/actividades/controller/lista_activ.php` |
| `list_cjto`, `list_cjto_sacd` | `frontend/asistentes/controller/lista_asis_conjunto_activ.php` |
| (resto) | `frontend/actividades/controller/actividad_select.php` |

Titulo segun `modo`: buscar actividad / buscar actividad de otras dl para
importar / buscar actividades de mi dl para publicar.

## Tipo

- Subtipo: `pantalla_principal` (se renderiza completa en `#main` via
  `ViewNewTwig`; varios bloques internos llegan por AJAX)
- Controller: `frontend/actividades/controller/actividad_que.php`
- Vista: `frontend/actividades/view/actividad_que.html.twig`

## Bloques cargados por AJAX

| Bloque | Endpoint | Cuando |
|--------|----------|--------|
| Selector de tipo de actividad | `/src/actividades/actividad_que_datos` | En servidor (PostRequest) al renderizar. |
| Filtros extra (lugar/organiza/publicada) | `/src/actividades/actividad_que_filtros` | Al cargar (`fnjs_cargar_filtros_extra`); vacio para roles de centro. |
| Cuadros de fases on/off | `/src/procesos/actividad_que_fases_ajax` | Al cargar y al cambiar tipo u organiza (`fnjs_actualizar_fases`); solo con `procesos`. |
| Desplegables de la cascada y lugar | `/src/actividades/actividad_tipo_get` | onchange de cada nivel (`fnjs_asistentes`, `fnjs_actividad`, `fnjs_nom_tipo`, `fnjs_id_activ`, `fnjs_lugar`). |

## Acciones (revisadas)

| Accion | Funcion JS | Llama a | Parametros |
|--------|-----------|---------|------------|
| Buscar | `fnjs_buscar(accion)` (boton `btn_ok`) | destino segun `que` (POST normal) | todo el formulario; antes compone `id_tipo_activ` = `isfsv+iasistentes+iactividad+inom_tipo` |
| Borrar filtros | boton reset | recarga `actividad_que.php` | `que`, `sactividad`, `sasistentes` |
| Cambiar sf/sv | `fnjs_asistentes()` | `actividad_tipo_get` (`salida=asistentes`, y `dl_org`/`filtro_lugar` si estan) | `entrada=isfsv` |
| Cambiar asistentes | `fnjs_actividad()` | `actividad_tipo_get` (`salida=actividad`) | `entrada=isfsv+iasistentes` |
| Cambiar actividad | `fnjs_nom_tipo()` | `actividad_tipo_get` (`salida=nom_tipo`) | `entrada=isfsv+iasistentes+iactividad` |
| Cambiar tipo | `fnjs_id_activ()` | `actividad_tipo_get` (`salida=id_tarifa`) + actualiza fases | `entrada=id_tipo_activ` |
| Cambiar lugar pais/dl | `fnjs_lugar()` (onchange `filtro_lugar`) | `actividad_tipo_get` (`salida=lugar`) | `entrada=filtro_lugar`, `opcion_sel`, `isfsv` |
| Actualizar fases | `fnjs_actualizar_fases(on, off)` | `/src/procesos/actividad_que_fases_ajax` (x2: on y off) | `dl_propia`, `id_tipo_activ`, `selected` |

## Validaciones en cliente

- Periodo `otro`: exige fecha inicio y fecha fin validas (`fnjs_comprobar_fecha`).
- Con `procesos`: una misma fase no puede estar a la vez en "marcadas" y
  "sin marcar" (`fnjs_comprobar_fase_no_duplicadas`).

## Permisos

- `perm_jefe` (jefe calendario, o `des`/`vcsd` en sv, o `admin_sf` en sf):
  amplia los asistentes visibles en la cascada.
- Sin permiso `des`: la cascada queda restringida a la seccion propia (sv/sf).
- Roles de centro (`isRolePau('ctr')`): no ven el bloque de filtros extra.

## Manual De Usuario

Ver [`manual/actividades.md`](../../../manual/actividades.md), secciones
*Buscar Y Abrir Actividades* e *Importar/Publicar*.

## Revision Manual

- Revisado jun 2026 (lectura de controller + twig + `_actividad_tipo.js`):
  subtipo corregido a pantalla principal, destinos por `que`, tabla de acciones
  y validaciones documentadas.
- Los campos `form.entrada`, `form.salida`, `form.opcion_sel`, `form.selected`,
  `form.dl_propia` que detectaba el generador pertenecen a las llamadas AJAX,
  no al formulario de la pantalla.
