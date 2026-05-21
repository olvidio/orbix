---
id: "menus.pantalla.menus_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "menus"
nombre: "Menus Que"
controller: "frontend/menus/controller/menus_que.php"
vistas: ["frontend/menus/view/menus_que.phtml"]
fragmentos_frontend: ["frontend/menus/controller/menus_get.php"]
endpoints: ["/src/menus/grupmenu_lista"]
capacidades: ["menus.grupmenu.gestionar"]
campos: ["form.filtro_grupo", "post.filtro_grupo"]
acciones: ["fnjs_lista_menus"]
estado_revision: "generado"
---

# Menus Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/menus/controller/menus_que.php`

## Vistas Relacionadas

- `frontend/menus/view/menus_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/menus/controller/menus_get.php`

## Endpoints Usados

- `/src/menus/grupmenu_lista`

## Capacidades Relacionadas

- `menus.grupmenu.gestionar`

## Campos Detectados

- `form.filtro_grupo`
- `post.filtro_grupo`

## Acciones Detectadas

- `fnjs_lista_menus`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
