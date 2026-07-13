---
id: "actividades.pantalla.lista_activ"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Listado de actividades (tabla)"
controller: "frontend/actividades/controller/lista_activ.php"
vistas: ["frontend/actividades/view/lista_activ.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividades/lista_activ_datos"]
capacidades: ["actividades.lista_activ.gestionar"]
campos: ["post.Gstack", "post.asist", "post.c_activ", "post.continuar", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.filtro_lugar", "post.id_tipo_activ", "post.id_ubi", "post.periodo", "post.que", "post.sactividad", "post.sasistentes", "post.seccion", "post.snom_tipo", "post.ssfsv", "post.stack", "post.status", "post.titulo", "post.year"]
acciones: []
estado_revision: "revisado"
---

# Listado de actividades (tabla)

Pantalla de **resultados tabulares** de actividades. Recibe por POST los filtros de
`actividad_que` (`que=list_activ` / `list_activ_compl`) o de `lista_activ_que`
(listados SR/SG) y llama a `lista_activ_datos` para obtener cabeceras, filas y
título; monta la tabla con `Lista` y la muestra con navegación atrás.

Si no llega periodo con fechas, fija `periodo=curso` y calcula `empiezamin` /
`empiezamax` con `Periodo`.

## Tipo

- Subtipo: `pantalla_principal` (render completo en `#main` con `ViewNewPhtml`)
- Controller: `frontend/actividades/controller/lista_activ.php`
- Vista: `frontend/actividades/view/lista_activ.phtml`

## Endpoints Usados

- `/src/actividades/lista_activ_datos` — payload con `a_cabeceras`, `a_valores`
  (celdas pueden llevar `link_spec` firmado) y `titulo`.

## Manual De Usuario

Ver [`manual/actividades.md`](../../../manual/actividades.md). Flujo habitual:

1. El usuario rellena filtros en `actividad_que` o `lista_activ_que` y pulsa buscar.
2. El formulario POSTea a esta pantalla, que consulta el backend y muestra la tabla.
3. Los enlaces de fila (si los hay) abren la ficha u otras pantallas según `link_spec`.

## Ruta de menú

No tiene entrada directa: se alcanza como **destino del formulario** de búsqueda
(`actividad_que` con `que=list_activ`) o desde `lista_activ_que` (p. ej. listas SG/SR).

- **Legacy:** dre > actividades > buscar activ (resultado); Calendario > actividades >
  listas sg (vía `lista_activ_que`).
- **Pills2:** ACTIVIDADES > Listados > Listas asistentes sg (vía `lista_activ_que`);
  resto de búsquedas heredan la ruta de `actividad_que`.
