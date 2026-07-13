---
id: "certificados.certificado_emitido_imprimir_mpdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Imprimir Mpdf"
capacidad: "certificados.certificado_emitido_imprimir_mpdf.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_imprimir_mpdf_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Imprimir Mpdf

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Generar el PDF del certificado con notas y textos traducidos.

## Punto De Entrada

Paso posterior a `certificado_emitido_imprimir`.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_imprimir_mpdf`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_imprimir_mpdf_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
