---
id: "certificados.pantalla.certificado_recibido_modificar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "certificados"
nombre: "Certificado Recibido Modificar"
controller: "frontend/certificados/controller/certificado_recibido_modificar.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/certificados/certificado_recibido_guardar", "/src/certificados/certificado_recibido_modificar_data", "/src/certificados/certificado_recibido_pdf_upload"]
capacidades: ["certificados.certificado_recibido.gestionar", "certificados.certificado_recibido_modificar.gestionar", "certificados.certificado_recibido_pdf_upload.gestionar"]
campos: ["form.certificado", "form.certificado_pdf", "form.f_certificado", "form.f_recibido", "form.firmado", "form.idioma"]
acciones: []
estado_revision: "generado"
---

# Certificado Recibido Modificar

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/certificados/controller/certificado_recibido_modificar.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/certificados/certificado_recibido_guardar`
- `/src/certificados/certificado_recibido_modificar_data`
- `/src/certificados/certificado_recibido_pdf_upload`

## Capacidades Relacionadas

- `certificados.certificado_recibido.gestionar`
- `certificados.certificado_recibido_modificar.gestionar`
- `certificados.certificado_recibido_pdf_upload.gestionar`

## Campos Detectados

- `form.certificado`
- `form.certificado_pdf`
- `form.f_certificado`
- `form.f_recibido`
- `form.firmado`
- `form.idioma`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
