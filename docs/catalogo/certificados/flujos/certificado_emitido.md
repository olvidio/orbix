---
id: "certificados.certificado_emitido.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido"
capacidad: "certificados.certificado_emitido.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/certificados/certificado_emitido_delete", "/src/certificados/certificado_emitido_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Imprimir, guardar o eliminar un certificado emitido desde el formulario de impresión.

## Punto De Entrada

Desde dossier de persona o flujo de impresión (`certificado_emitido_imprimir`).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_imprimir`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/certificados/certificado_emitido_delete`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.destino`
- `form.f_certificado`
- `form.firmado`
- `form.guardar`
- `form.id_item`
- `form.idioma`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_delete`
- `/src/certificados/certificado_emitido_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
