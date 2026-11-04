---
id: "certificados.certificado_recibido_modificar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Modificar un certificado recibido"
capacidad: "certificados.certificado_recibido_modificar.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_recibido_modificar"]
fragmentos: ["certificados.pantalla.certificado_recibido_modificar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_recibido_modificar_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido Modificar

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Corregir los datos de un certificado recibido.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_modificar`

## Escenarios Inferidos

### Modificar un certificado recibido

Pasos propuestos:
1. En el dossier de certificados, pulsar el certificado recibido.
2. Cambiar los datos y guardar.

Endpoints asociados:
- `/src/certificados/certificado_recibido_modificar_data`

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
