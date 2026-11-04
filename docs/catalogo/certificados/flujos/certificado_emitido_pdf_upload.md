---
id: "certificados.certificado_emitido_pdf_upload.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Subir el PDF de un certificado"
capacidad: "certificados.certificado_emitido_pdf_upload.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_pdf_upload"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_emitido_pdf_upload"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Pdf Upload

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Subir el fichero PDF de un certificado emitido.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Subir el PDF de un certificado

Pasos propuestos:
1. Elegir el fichero PDF en el formulario de adjuntar.
2. Guardar. Si es demasiado grande el servidor lo rechaza con un aviso de tamaño.

Endpoints asociados:
- `/src/certificados/certificado_emitido_pdf_upload`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_pdf_upload`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
