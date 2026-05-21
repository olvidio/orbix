---
id: "actividades.pantalla.lista_actividades_sg"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Lista Actividades Sg"
controller: "frontend/actividades/controller/lista_actividades_sg.php"
vistas: ["frontend/actividades/view/lista_actividades_sg.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/lista_actividades_sg.php"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
capacidades: ["actividades.lista_actividades_sg.gestionar"]
campos: ["form.mod", "form.queSel", "form.sel", "html.b_buscar", "html.mod", "post.Gstack", "post.continuar", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.filtro_lugar", "post.id_ubi", "post.periodo", "post.que", "post.scroll_id", "post.sel", "post.stack", "post.status", "post.tipo_activ_sg", "post.year"]
acciones: ["button:. _(", "fnjs_borrar", "fnjs_buscar", "fnjs_enviar_formulario", "fnjs_solo_uno", "fnjs_update_div"]
estado_revision: "generado"
---

# Lista Actividades Sg

Pantalla que lista actividades sf/sg (crt, cv) con los filtros de periodo, tipo, lugar y delegacion.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/lista_actividades_sg.php`

## Vistas Relacionadas

- `frontend/actividades/view/lista_actividades_sg.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/lista_actividades_sg.php`

## Endpoints Usados

- `/src/actividades/lista_actividades_sg_datos`

## Capacidades Relacionadas

- `actividades.lista_actividades_sg.gestionar`

## Campos Detectados

- `form.mod`
- `form.queSel`
- `form.sel`
- `html.b_buscar`
- `html.mod`
- `post.Gstack`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.filtro_lugar`
- `post.id_ubi`
- `post.periodo`
- `post.que`
- `post.scroll_id`
- `post.sel`
- `post.stack`
- `post.status`
- `post.tipo_activ_sg`
- `post.year`

## Acciones Detectadas

- `button:. _(`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
