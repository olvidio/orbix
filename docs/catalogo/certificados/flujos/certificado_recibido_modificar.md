---
id: "certificados.certificado_recibido_modificar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Recibido Modificar"
capacidad: "certificados.certificado_recibido_modificar.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_recibido_modificar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_recibido_modificar_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido Modificar

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Modificar metadatos de certificado recibido.

## Punto De Entrada

Listado dossier persona → modificar.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_modificar`

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

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_recibido_modificar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
