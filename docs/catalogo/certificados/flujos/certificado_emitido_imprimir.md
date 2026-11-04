---
id: "certificados.certificado_emitido_imprimir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Emitir un certificado nuevo"
capacidad: "certificados.certificado_emitido_imprimir.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_imprimir"]
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_imprimir_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Imprimir

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Preparar los datos de un certificado nuevo de un alumno y empezar su impresión.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_imprimir`

## Escenarios Inferidos

### Emitir un certificado nuevo

Pasos propuestos:
1. Abrir la ficha del alumno.
2. En el dossier de certificados elegir emitir un certificado nuevo.
3. Elegir el idioma y los datos del certificado y pulsar imprimir.
4. Se genera el PDF con las notas y los textos legales en el idioma elegido.

Endpoints asociados:
- `/src/certificados/certificado_emitido_imprimir_datos`

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

- `/src/certificados/certificado_emitido_imprimir_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
