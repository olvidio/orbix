---
id: "certificados.certificados_locales.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Elegir el idioma del certificado"
capacidad: "certificados.certificados_locales.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_adjuntar", "certificados.pantalla.certificado_recibido_adjuntar"]
fragmentos: ["certificados.pantalla.certificado_emitido_adjuntar", "certificados.pantalla.certificado_recibido_adjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificados_locales_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificados Locales

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Cargar la lista de idiomas disponibles en los formularios de certificados.

## Punto De Entrada

Formularios de certificados.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_adjuntar`
- `certificados.pantalla.certificado_recibido_adjuntar`

## Escenarios Inferidos

### Elegir el idioma del certificado

Pasos propuestos:
1. En el formulario de certificado, abrir el desplegable de idioma.
2. Elegir el idioma en que se emitirá.

Endpoints asociados:
- `/src/certificados/certificados_locales_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.certificado_pdf`
- `form.f_certificado`
- `form.f_enviado`
- `form.f_recibido`
- `form.firmado`
- `form.idioma`
- `post.id_nom`
- `post.nuevo`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificados_locales_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
