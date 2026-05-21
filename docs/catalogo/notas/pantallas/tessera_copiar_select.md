---
id: "notas.pantalla.tessera_copiar_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Tessera Copiar Select"
controller: "frontend/notas/controller/tessera_copiar_select.php"
vistas: ["frontend/notas/view/tessera_copiar_select.phtml"]
fragmentos_frontend: []
endpoints: ["/src/notas/tessera_copiar", "/src/notas/tessera_copiar_select_data"]
capacidades: ["notas.tessera.gestionar", "notas.tessera_copiar_select.gestionar"]
campos: ["form.id_nom_dst", "html.copiar", "post.id_nom", "post.id_tabla", "post.sel", "post.stack"]
acciones: ["fnjs_copiar", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Tessera Copiar Select

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/tessera_copiar_select.php`

## Vistas Relacionadas

- `frontend/notas/view/tessera_copiar_select.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/tessera_copiar`
- `/src/notas/tessera_copiar_select_data`

## Capacidades Relacionadas

- `notas.tessera.gestionar`
- `notas.tessera_copiar_select.gestionar`

## Campos Detectados

- `form.id_nom_dst`
- `html.copiar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_copiar`
- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
