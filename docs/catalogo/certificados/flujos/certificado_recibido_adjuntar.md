---
id: "certificados.certificado_recibido_adjuntar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Recibido Adjuntar"
capacidad: "certificados.certificado_recibido_adjuntar.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_recibido_adjuntar_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido Adjuntar

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Registrar un certificado recibido nuevo con PDF.

## Punto De Entrada

Listado certificados de persona → nuevo.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_adjuntar`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/certificados/certificado_recibido_adjuntar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
