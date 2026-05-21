---
id: "menus.pantalla.menus_importar_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "menus"
nombre: "Menus Importar Form"
controller: "frontend/menus/controller/menus_importar_form.php"
vistas: ["frontend/menus/view/menus_importar_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/menus/lista_templates", "/src/menus/menus_importar"]
capacidades: ["menus.lista_templates.gestionar", "menus.menus_importar.gestionar"]
campos: ["form.id_template_menu", "html.btn_ok"]
acciones: ["fnjs_enviar", "fnjs_importar"]
estado_revision: "generado"
---

# Menus Importar Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/menus/controller/menus_importar_form.php`

## Vistas Relacionadas

- `frontend/menus/view/menus_importar_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/menus/lista_templates`
- `/src/menus/menus_importar`

## Capacidades Relacionadas

- `menus.lista_templates.gestionar`
- `menus.menus_importar.gestionar`

## Campos Detectados

- `form.id_template_menu`
- `html.btn_ok`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_importar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
