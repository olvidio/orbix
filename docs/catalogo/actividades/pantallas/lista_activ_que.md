---
id: "actividades.pantalla.lista_activ_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Filtros listados SR/SG"
controller: "frontend/actividades/controller/lista_activ_que.php"
vistas: ["frontend/actividades/view/lista_activ_que.html.twig"]
fragmentos_frontend: ["frontend/actividades/controller/lista_activ.php"]
endpoints: ["/src/actividades/lista_activ_datos"]
capacidades: ["actividades.lista_activ.gestionar"]
campos: ["form.asist", "form.c_activ", "form.empiezamax", "form.empiezamin", "form.seccion", "form.status", "form.tit_list_grupo", "post.que"]
acciones: []
estado_revision: "revisado"
---

# Filtros listados SR/SG

Formulario de **filtros para listados especiales** de SR/SG. Según `que`:

| `que` | Título / uso |
|-------|----------------|
| `list_activ_inv_sg` | Listado actividades San Gabriel |
| `list_activ_sr` | Listado SR (SV) |
| `list_activ_sr_sf` | Listado SR SF |

El POST del formulario va a `lista_activ.php`, que consume `lista_activ_datos`.
Campos: secciones, estados, periodo, tipos de actividad (`c_activ`), asistentes
(`asist`), título de grupo (`tit_list_grupo`). Con permiso `des` se amplían opciones.

## Tipo

- Subtipo: `pantalla_principal` (`ViewNewTwig`)
- Controller: `frontend/actividades/controller/lista_activ_que.php`

## Manual De Usuario

Elegir criterios del listado (p. ej. listas SG del calendario) y buscar; la tabla
aparece en `lista_activ`.

## Ruta de menú

- **Legacy:** Calendario > actividades > listas sg; dre > actividades > listas sg.
- **Pills2:** ACTIVIDADES > Listados > Listas asistentes sg (`que=list_activ_inv_sg`).
