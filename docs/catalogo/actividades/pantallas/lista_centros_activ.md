---
id: "actividades.pantalla.lista_centros_activ"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Listado actividades por centro"
controller: "frontend/actividades/controller/lista_centros_activ.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/lista_centros_activ.php"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
capacidades: ["actividades.lista_centros_activ.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_ctr", "post.id_ctr_num", "post.periodo", "post.year"]
acciones: []
estado_revision: "revisado"
---

# Listado actividades por centro

Fragmento **HTML devuelto por AJAX** (`AjaxJsonSupport::html`) con el listado de
centros seleccionados y sus actividades en el periodo indicado. Se invoca desde
`actividades_centro_que` cuando `tipo_lista` es `crt` o `cv`; el HTML se inyecta
en `#exportar`.

## Tipo

- Subtipo: `fragmento_ajax` (sin vista propia: el controller echoea el HTML del backend)
- Controller: `frontend/actividades/controller/lista_centros_activ.php`

## Endpoints Usados

- `/src/actividades/lista_centros_activ_datos` — devuelve clave `html` lista para insertar.

## Manual De Usuario

1. En *de cada ctr* (`actividades_centro_que`), el usuario elige uno o más centros y periodo.
2. Pulsa **buscar** (`fnjs_ver`); el POST llega aquí y el resultado sustituye `#exportar`.
3. Desde el listado puede abrir modificar/nueva actividad vía `centro_ajax.php` (popup).

## Ruta de menú

Sin entrada propia; acceso vía `actividades_centro_que`:

- **Legacy:** vsg > crt/cv > de cada ctr.
- **Pills2:** sin entrada dedicada en el índice (misma ruta legacy vsg).
