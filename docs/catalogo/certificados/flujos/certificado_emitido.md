---
id: "certificados.certificado_emitido.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Emitir, guardar o eliminar un certificado"
capacidad: "certificados.certificado_emitido.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_imprimir"]
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

Ficha del alumno > dossier de certificados.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_imprimir`

## Escenarios Inferidos

### Emitir, guardar o eliminar un certificado

Pasos propuestos:
1. Abrir el formulario de impresión del certificado.
2. Pulsar imprimir para generar el PDF y guardar para registrarlo con su número.
3. Para quitarlo, eliminarlo y confirmar.

Endpoints asociados:
- `/src/certificados/certificado_emitido_delete`
- `/src/certificados/certificado_emitido_guardar`

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
