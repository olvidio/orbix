---
id: "certificados.pantalla.certificado_emitido_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "certificados"
nombre: "Certificado Emitido Ver"
controller: "frontend/certificados/controller/certificado_emitido_ver.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/certificados/certificado_emitido_ver_datos", "/src/shared/locales_posibles"]
capacidades: ["certificados.certificado_emitido_ver.gestionar"]
campos: ["form.certificado", "form.certificado_pdf", "form.destino", "form.f_certificado", "form.f_enviado", "form.firmado", "form.idioma", "form.nom", "post.sel"]
acciones: []
estado_revision: "generado"
---

# Certificado Emitido Ver

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/certificados/controller/certificado_emitido_ver.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/certificados/certificado_emitido_ver_datos`
- `/src/shared/locales_posibles`

## Capacidades Relacionadas

- `certificados.certificado_emitido_ver.gestionar`

## Campos Detectados

- `form.certificado`
- `form.certificado_pdf`
- `form.destino`
- `form.f_certificado`
- `form.f_enviado`
- `form.firmado`
- `form.idioma`
- `form.nom`
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
