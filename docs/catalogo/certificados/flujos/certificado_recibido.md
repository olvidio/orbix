---
id: "certificados.certificado_recibido.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Recibido"
capacidad: "certificados.certificado_recibido.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_recibido_adjuntar", "certificados.pantalla.certificado_recibido_modificar"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/certificados/certificado_recibido_delete", "/src/certificados/certificado_recibido_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Recibido

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Gestionar certificados recibidos de una persona (adjuntar, modificar, eliminar).

## Punto De Entrada

Dossier persona 1010 (`select_certificados_de_una_persona`).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/certificados/certificado_recibido_delete`

### Guardar

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

- `/src/certificados/certificado_recibido_delete`
- `/src/certificados/certificado_recibido_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
