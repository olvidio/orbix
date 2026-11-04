---
id: "certificados.certificado_emitido_adjuntar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Adjuntar un certificado emitido"
capacidad: "certificados.certificado_emitido_adjuntar.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_adjuntar"]
fragmentos: ["certificados.pantalla.certificado_emitido_adjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_adjuntar_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Adjuntar

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Adjuntar a una persona un PDF de un certificado ya emitido.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_adjuntar`

## Escenarios Inferidos

### Adjuntar un certificado emitido

Pasos propuestos:
1. Abrir la ficha de la persona y su dossier de certificados.
2. Pulsar **adjuntar nuevo certificado**.
3. Rellenar los datos y elegir el PDF.
4. Guardar.

Endpoints asociados:
- `/src/certificados/certificado_emitido_adjuntar_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.certificado_pdf`
- `form.f_certificado`
- `form.f_enviado`
- `form.firmado`
- `form.idioma`
- `post.id_nom`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_adjuntar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
