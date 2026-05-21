---
id: "certificados.pantalla.certificado_emitido_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "certificados"
nombre: "Certificado Emitido Lista"
controller: "frontend/certificados/controller/certificado_emitido_lista.php"
vistas: ["frontend/certificados/view/certificado_emitido_lista.phtml"]
fragmentos_frontend: ["frontend/certificados/controller/certificado_emitido_lista.php", "frontend/certificados/controller/certificado_emitido_upload_firmado.php", "frontend/certificados/controller/certificado_emitido_ver.php"]
endpoints: ["/src/certificados/certificado_emitido_lista_datos"]
capacidades: ["certificados.certificado_emitido_lista.gestionar"]
campos: ["form.certificado", "form.mod", "form.sel", "html.btn_ok", "html.certificado", "html.mod", "html.refresh", "post.certificado", "post.refresh", "post.stack", "post.titulo"]
acciones: ["fnjs_actualizar", "fnjs_descargar_pdf", "fnjs_eliminar", "fnjs_enviar", "fnjs_enviar_certificado", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_nuevo", "fnjs_solo_uno", "fnjs_upload_certificado"]
estado_revision: "generado"
---

# Certificado Emitido Lista

Esta página muestra una tabla con los certificados.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/certificados/controller/certificado_emitido_lista.php`

## Vistas Relacionadas

- `frontend/certificados/view/certificado_emitido_lista.phtml`

## Fragmentos Frontend Relacionados

- `frontend/certificados/controller/certificado_emitido_lista.php`
- `frontend/certificados/controller/certificado_emitido_upload_firmado.php`
- `frontend/certificados/controller/certificado_emitido_ver.php`

## Endpoints Usados

- `/src/certificados/certificado_emitido_lista_datos`

## Capacidades Relacionadas

- `certificados.certificado_emitido_lista.gestionar`

## Campos Detectados

- `form.certificado`
- `form.mod`
- `form.sel`
- `html.btn_ok`
- `html.certificado`
- `html.mod`
- `html.refresh`
- `post.certificado`
- `post.refresh`
- `post.stack`
- `post.titulo`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_certificado`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`
- `fnjs_upload_certificado`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
