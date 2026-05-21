---
id: "menus.pantalla.menus_exportar_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "menus"
nombre: "Menus Exportar Form"
controller: "frontend/menus/controller/menus_exportar_form.php"
vistas: ["frontend/menus/view/menus_exportar_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/menus/menus_exportar"]
capacidades: ["menus.menus_exportar.gestionar"]
campos: ["form.nombre", "html.btn_ok", "html.nombre"]
acciones: ["fnjs_enviar", "fnjs_guardar"]
estado_revision: "generado"
---

# Menus Exportar Form

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/menus/controller/menus_exportar_form.php`

## Vistas Relacionadas

- `frontend/menus/view/menus_exportar_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/menus/menus_exportar`

## Capacidades Relacionadas

- `menus.menus_exportar.gestionar`

## Campos Detectados

- `form.nombre`
- `html.btn_ok`
- `html.nombre`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
