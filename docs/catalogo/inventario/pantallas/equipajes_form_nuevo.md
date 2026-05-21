---
id: "inventario.pantalla.equipajes_form_nuevo"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Form Nuevo"
controller: "frontend/inventario/controller/equipajes_form_nuevo.php"
vistas: ["frontend/inventario/view/equipajes_form_nuevo.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/equipajes_lista_activ_sel"]
capacidades: ["inventario.equipajes_lista_activ_sel.gestionar"]
campos: ["form.nom_equipaje", "html.nom_equipaje", "post.id_cdc", "post.nom_equip", "post.sel"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "generado"
---

# Equipajes Form Nuevo

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_nuevo.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_nuevo.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/equipajes_lista_activ_sel`

## Capacidades Relacionadas

- `inventario.equipajes_lista_activ_sel.gestionar`

## Campos Detectados

- `form.nom_equipaje`
- `html.nom_equipaje`
- `post.id_cdc`
- `post.nom_equip`
- `post.sel`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
