---
id: "ubis.pantalla.ubis_buscar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Ubis Buscar"
controller: "frontend/ubis/controller/ubis_buscar.php"
vistas: ["frontend/ubis/view/ubis_buscar.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/ubis_buscar.php", "frontend/ubis/controller/ubis_tabla.php"]
endpoints: ["/src/ubis/ubis_buscar_data"]
capacidades: ["ubis.ubis_buscar.gestionar"]
campos: ["html.b_buscar", "html.b_mas", "html.cmb", "html.labor[]", "html.loc", "html.ok", "html.opcion", "html.select[]", "html.simple", "html.tipo", "post.loc", "post.simple", "post.tipo"]
acciones: ["fnjs_actualizar", "fnjs_buscar", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_update_div", "fnjs_ver_solo"]
estado_revision: "generado"
---

# Ubis Buscar

Es un formulario para introducir las condiciones de búsqueda de los ubis.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/ubis_buscar.php`

## Vistas Relacionadas

- `frontend/ubis/view/ubis_buscar.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/ubis_buscar.php`
- `frontend/ubis/controller/ubis_tabla.php`

## Endpoints Usados

- `/src/ubis/ubis_buscar_data`

## Capacidades Relacionadas

- `ubis.ubis_buscar.gestionar`

## Campos Detectados

- `html.b_buscar`
- `html.b_mas`
- `html.cmb`
- `html.labor[]`
- `html.loc`
- `html.ok`
- `html.opcion`
- `html.select[]`
- `html.simple`
- `html.tipo`
- `post.loc`
- `post.simple`
- `post.tipo`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_update_div`
- `fnjs_ver_solo`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
