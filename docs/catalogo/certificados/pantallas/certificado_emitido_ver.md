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
estado_revision: "revisado"
---

# Certificado Emitido Ver

Vista Twig de consulta/edición de un certificado emitido (metadatos y PDF embebido). Abierta desde el listado de emitidos.

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

## Ruta de menú

- sin entrada de menú en el índice (modal desde listado Certificados)
