---
id: "actividades.pantalla.actividad_select_ubi"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Seleccionar lugar (popup)"
controller: "frontend/actividades/controller/actividad_select_ubi.php"
vistas: ["frontend/actividades/view/actividad_select_ubi.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/ubis_lista.php"]
endpoints: ["/src/actividades/actividad_select_ubi_desplegable", "/src/actividades/actividad_tipo_get"]
capacidades: ["actividades.actividad_select_ubi_desplegable.gestionar", "actividades.actividad_tipo.gestionar"]
campos: ["form.dl_org", "form.entrada", "form.extendida", "form.filtro_lugar", "form.frm_4_nombre_ubi", "form.id_ubi_1", "form.isfsv", "form.lst_lugar", "form.modo", "form.nombre_ubi", "form.salida", "form.tipo", "html.b_buscar"]
acciones: ["fnjs_buscar", "fnjs_cargar_desplegable", "fnjs_construir_desplegable", "fnjs_enviar_form", "fnjs_lugar"]
estado_revision: "revisado"
---

# Seleccionar lugar (popup)

Ventana auxiliar para **elegir ubicación** de una actividad. Cinco modos: historial
reciente, región, búsqueda por nombre (enlace a `ubis_lista.php`), lugar especial o
*por determinar*. Los desplegables dinámicos cargan vía
`actividad_select_ubi_desplegable`; la cascada país/dl usa `actividad_tipo_get`
(`salida=lugar`). Al aceptar, devuelve `id_ubi` y texto al `window.opener`
(ficha `actividad_ver`, planning, etc.).

## Tipo

- Subtipo: `pantalla_principal` (popup independiente, no fragmento del `#main`)
- Controller: `frontend/actividades/controller/actividad_select_ubi.php`
- Vista: `frontend/actividades/view/actividad_select_ubi.phtml`

## Endpoints Usados

- `/src/actividades/actividad_select_ubi_desplegable` — opciones freq/región
- `/src/actividades/actividad_tipo_get` — desplegable lugar según filtro

## Manual De Usuario

Desde la ficha de actividad (span lugar) o planning: elegir modo, buscar casa/lugar,
confirmar. Aviso JS si el nombre de actividad puede quedar desactualizado.

## Ruta de menú

sin entrada de menú en el índice (popup invocado desde ficha/planning).
