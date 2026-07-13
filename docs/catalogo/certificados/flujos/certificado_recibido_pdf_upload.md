---
id: "certificados.certificado_recibido_pdf_upload.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Recibido Pdf Upload"
capacidad: "certificados.certificado_recibido_pdf_upload.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_recibido_pdf_upload"]
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_recibido_pdf_upload"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido Pdf Upload

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Subir PDF de certificado recibido.

## Punto De Entrada

Formulario adjuntar recibido.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.certificado_pdf`
- `form.f_certificado`
- `form.f_recibido`
- `form.firmado`
- `form.idioma`
- `post.id_nom`
- `post.nuevo`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_recibido_pdf_upload`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
