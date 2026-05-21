---
id: "inventario.pantalla.equipajes_form_texto_listado"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Form Texto Listado"
controller: "frontend/inventario/controller/equipajes_form_texto_listado.php"
vistas: ["frontend/inventario/view/equipajes_form_texto_listado.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/texto_de_egm"]
capacidades: ["inventario.texto_de_egm.gestionar"]
campos: ["form.texto", "html.texto", "post.id_equipaje", "post.loc", "post.texto"]
acciones: ["fnjs_cerrar", "fnjs_guardar_listado"]
estado_revision: "generado"
---

# Equipajes Form Texto Listado

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_texto_listado.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_texto_listado.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/texto_de_egm`

## Capacidades Relacionadas

- `inventario.texto_de_egm.gestionar`

## Campos Detectados

- `form.texto`
- `html.texto`
- `post.id_equipaje`
- `post.loc`
- `post.texto`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar_listado`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
