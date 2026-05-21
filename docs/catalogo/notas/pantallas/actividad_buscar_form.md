---
id: "notas.pantalla.actividad_buscar_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Actividad Buscar Form"
controller: "frontend/notas/controller/actividad_buscar_form.php"
vistas: ["frontend/notas/view/actividad_buscar_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/notas/actividades_buscar_data"]
capacidades: ["notas.actividades_buscar.gestionar"]
campos: ["form.observ", "form.pres_mail", "form.pres_nom", "form.pres_telf", "form.zona", "post.dl_org", "post.f_acta_iso", "post.id_activ"]
acciones: ["fnjs_buscar_ca", "fnjs_cerrar", "fnjs_update_activ"]
estado_revision: "generado"
---

# Actividad Buscar Form

Dialogo "buscar actividad" que abre `form_notas_de_una_persona.phtml` al pulsar "añadir ca".

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/actividad_buscar_form.php`

## Vistas Relacionadas

- `frontend/notas/view/actividad_buscar_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/actividades_buscar_data`

## Capacidades Relacionadas

- `notas.actividades_buscar.gestionar`

## Campos Detectados

- `form.observ`
- `form.pres_mail`
- `form.pres_nom`
- `form.pres_telf`
- `form.zona`
- `post.dl_org`
- `post.f_acta_iso`
- `post.id_activ`

## Acciones Detectadas

- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_update_activ`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
