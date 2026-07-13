---
id: "certificados.certificado_emitido_imprimir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Imprimir"
capacidad: "certificados.certificado_emitido_imprimir.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_imprimir_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Imprimir

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Preparar datos e iniciar impresión de certificado nuevo para un alumno.

## Punto De Entrada

Dossier persona / matrículas.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_imprimir`

## Escenarios Inferidos

### Obtener Datos

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

- `/src/certificados/certificado_emitido_imprimir_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
