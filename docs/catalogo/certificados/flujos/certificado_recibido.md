---
id: "certificados.certificado_recibido.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar certificados recibidos"
capacidad: "certificados.certificado_recibido.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/certificados/certificado_recibido_delete", "/src/certificados/certificado_recibido_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Gestionar los certificados recibidos de una persona: adjuntar, modificar y eliminar.

## Punto De Entrada

Ficha de la persona > dossier de certificados.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

## Escenarios Inferidos

### Gestionar certificados recibidos

Pasos propuestos:
1. Adjuntar: pulsar **adjuntar nuevo certificado**, rellenar y subir el PDF.
2. Modificar: abrir el certificado, cambiar los datos y guardar.
3. Eliminar: pulsarlo y confirmar «¿seguro que desea borrar el certificado?».

Endpoints asociados:
- `/src/certificados/certificado_recibido_delete`
- `/src/certificados/certificado_recibido_guardar`

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

- `/src/certificados/certificado_recibido_delete`
- `/src/certificados/certificado_recibido_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
