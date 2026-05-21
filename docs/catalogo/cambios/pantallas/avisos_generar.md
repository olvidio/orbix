---
id: "cambios.pantalla.avisos_generar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Avisos Generar"
controller: "frontend/cambios/controller/avisos_generar.php"
vistas: ["frontend/cambios/view/avisos_generar.phtml"]
fragmentos_frontend: ["frontend/cambios/controller/avisos_generar.php"]
endpoints: ["/src/cambios/avisos_generar_lista_data"]
capacidades: ["cambios.avisos_generar.gestionar"]
campos: ["form.aviso_tipo", "form.id_usuario", "html.f_fin", "html.refresh", "post.Gstack", "post.aviso_tipo", "post.id_usuario", "post.refresh"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_borrar_hasta_fecha", "fnjs_enviar_formulario", "fnjs_selectAll"]
estado_revision: "generado"
---

# Avisos Generar

Pantalla: listado de avisos (cambios anotados) del usuario conectado o, para admins, del usuario seleccionado en el formulario superior.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/avisos_generar.php`

## Vistas Relacionadas

- `frontend/cambios/view/avisos_generar.phtml`

## Fragmentos Frontend Relacionados

- `frontend/cambios/controller/avisos_generar.php`

## Endpoints Usados

- `/src/cambios/avisos_generar_lista_data`

## Capacidades Relacionadas

- `cambios.avisos_generar.gestionar`

## Campos Detectados

- `form.aviso_tipo`
- `form.id_usuario`
- `html.f_fin`
- `html.refresh`
- `post.Gstack`
- `post.aviso_tipo`
- `post.id_usuario`
- `post.refresh`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_borrar_hasta_fecha`
- `fnjs_enviar_formulario`
- `fnjs_selectAll`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
