---
id: "ubis.pantalla.ubis_tabla"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "ubis"
nombre: "Ubis Tabla"
controller: "frontend/ubis/controller/ubis_tabla.php"
vistas: ["frontend/ubis/view/ubis_tabla.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/home_ubis.php", "frontend/ubis/controller/trasladar_ubis.php"]
endpoints: ["/src/ubis/ubis_tabla_data"]
capacidades: ["ubis.ubis_tabla.gestionar"]
campos: ["form.sel", "html.b_mas", "post.stack"]
acciones: ["fnjs_borrar", "fnjs_enviar_formulario", "fnjs_modificar", "fnjs_solo_uno", "fnjs_trasladar", "fnjs_update_div"]
estado_revision: "revisado"
---

# Ubis Tabla

Tabla de resultados de búsqueda de ubis con navegación y acciones sobre selección.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/ubis/controller/ubis_tabla.php`

## Vistas Relacionadas

- `frontend/ubis/view/ubis_tabla.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/home_ubis.php`
- `frontend/ubis/controller/trasladar_ubis.php`

## Endpoints Usados

- `/src/ubis/ubis_tabla_data`

## Capacidades Relacionadas

- `ubis.ubis_tabla.gestionar`

## Campos Detectados

- `form.sel`
- `html.b_mas`
- `post.stack`

## Acciones Detectadas

- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
