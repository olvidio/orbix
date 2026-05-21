---
id: "notas.pantalla.tessera_imprimir"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Tessera Imprimir"
controller: "frontend/notas/controller/tessera_imprimir.php"
vistas: []
fragmentos_frontend: ["frontend/notas/controller/tessera_2_mpdf.php", "frontend/notas/controller/tessera_imprimir.php"]
endpoints: ["/src/notas/tessera_imprimir_data"]
capacidades: ["notas.tessera_imprimir.gestionar"]
campos: ["post.cara", "post.id_nom", "post.id_tabla", "post.refresh", "post.sel", "post.stack"]
acciones: ["fnjs_left_side_hide", "fnjs_update_div"]
estado_revision: "generado"
---

# Tessera Imprimir

Esta página sirve para la tessera de una persona.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/tessera_imprimir.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/tessera_2_mpdf.php`
- `frontend/notas/controller/tessera_imprimir.php`

## Endpoints Usados

- `/src/notas/tessera_imprimir_data`

## Capacidades Relacionadas

- `notas.tessera_imprimir.gestionar`

## Campos Detectados

- `post.cara`
- `post.id_nom`
- `post.id_tabla`
- `post.refresh`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
