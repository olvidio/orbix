---
id: "certificados.certificado_recibido_adjuntar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Registrar un certificado recibido"
capacidad: "certificados.certificado_recibido_adjuntar.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_recibido_adjuntar"]
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_recibido_adjuntar_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido Adjuntar

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Registrar un certificado recibido de otra delegación, con su PDF.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_adjuntar`

## Escenarios Inferidos

### Registrar un certificado recibido

Pasos propuestos:
1. Abrir la ficha de la persona y su dossier de certificados.
2. Pulsar **adjuntar nuevo certificado** y elegir el tipo recibido.
3. Rellenar los datos y elegir el PDF.
4. Guardar.

Endpoints asociados:
- `/src/certificados/certificado_recibido_adjuntar_data`

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

- `/src/certificados/certificado_recibido_adjuntar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
