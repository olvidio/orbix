---
id: "certificados.pantalla.certificado_recibido_adjuntar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "certificados"
nombre: "Certificado Recibido Adjuntar"
controller: "frontend/certificados/controller/certificado_recibido_adjuntar.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/certificados/certificado_recibido_adjuntar_data", "/src/certificados/certificado_recibido_guardar", "/src/certificados/certificado_recibido_pdf_upload", "/src/certificados/certificados_locales_data"]
capacidades: ["certificados.certificado_recibido.gestionar", "certificados.certificado_recibido_adjuntar.gestionar", "certificados.certificado_recibido_pdf_upload.gestionar", "certificados.certificados_locales.gestionar"]
campos: ["form.certificado", "form.certificado_pdf", "form.f_certificado", "form.f_recibido", "form.firmado", "form.idioma", "post.id_nom", "post.nuevo", "post.sel"]
acciones: []
estado_revision: "generado"
---

# Certificado Recibido Adjuntar

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/certificados/controller/certificado_recibido_adjuntar.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/certificados/certificado_recibido_adjuntar_data`
- `/src/certificados/certificado_recibido_guardar`
- `/src/certificados/certificado_recibido_pdf_upload`
- `/src/certificados/certificados_locales_data`

## Capacidades Relacionadas

- `certificados.certificado_recibido.gestionar`
- `certificados.certificado_recibido_adjuntar.gestionar`
- `certificados.certificado_recibido_pdf_upload.gestionar`
- `certificados.certificados_locales.gestionar`

## Campos Detectados

- `form.certificado`
- `form.certificado_pdf`
- `form.f_certificado`
- `form.f_recibido`
- `form.firmado`
- `form.idioma`
- `post.id_nom`
- `post.nuevo`
- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
