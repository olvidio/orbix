---
id: "actividadestudios.pantalla.actualizar_docencia"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Actualizar Docencia"
controller: "frontend/actividadestudios/controller/actualizar_docencia.php"
vistas: ["frontend/actividadestudios/view/actualizar_docencia.phtml"]
fragmentos_frontend: ["frontend/actividadestudios/controller/actualizar_docencia.php"]
endpoints: ["/src/actividadestudios/docencia_actualizar"]
capacidades: ["actividadestudios.docencia_actualizar.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.year", "html.refresh", "post.continuar", "post.empiezamax", "post.empiezamin", "post.periodo", "post.year"]
acciones: ["fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Actualizar Docencia

Pantalla "actualizar docencia" (menu).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/actualizar_docencia.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/actualizar_docencia.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadestudios/controller/actualizar_docencia.php`

## Endpoints Usados

- `/src/actividadestudios/docencia_actualizar`

## Capacidades Relacionadas

- `actividadestudios.docencia_actualizar.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.continuar`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
