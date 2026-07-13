---
id: "certificados.pantalla.certificado_emitido_adjuntar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "certificados"
nombre: "Certificado Emitido Adjuntar"
controller: "frontend/certificados/controller/certificado_emitido_adjuntar.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/certificados/certificado_emitido_adjuntar_data", "/src/certificados/certificados_locales_data"]
capacidades: ["certificados.certificado_emitido_adjuntar.gestionar", "certificados.certificados_locales.gestionar"]
campos: ["form.certificado", "form.certificado_pdf", "form.f_certificado", "form.f_enviado", "form.firmado", "form.idioma", "post.id_nom", "post.sel"]
acciones: []
estado_revision: "revisado"
---

# Certificado Emitido Adjuntar

Formulario para adjuntar un certificado emitido (PDF + metadatos) a una persona. Entrada desde dossier o flujo de persona.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/certificados/controller/certificado_emitido_adjuntar.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/certificados/certificado_emitido_adjuntar_data`
- `/src/certificados/certificados_locales_data`

## Capacidades Relacionadas

- `certificados.certificado_emitido_adjuntar.gestionar`
- `certificados.certificados_locales.gestionar`

## Campos Detectados

- `form.certificado`
- `form.certificado_pdf`
- `form.f_certificado`
- `form.f_enviado`
- `form.firmado`
- `form.idioma`
- `post.id_nom`
- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- sin entrada de menú en el índice (dossier persona / navegación embebida)
