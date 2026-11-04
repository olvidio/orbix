---
id: "certificados.certificado_recibido_pdf_upload.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Subir el PDF de un certificado recibido"
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

Subir el PDF de un certificado recibido.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

## Escenarios Inferidos

### Subir el PDF de un certificado recibido

Pasos propuestos:
1. En el formulario de adjuntar, elegir el PDF y guardar.

Endpoints asociados:
- `/src/certificados/certificado_recibido_pdf_upload`

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
