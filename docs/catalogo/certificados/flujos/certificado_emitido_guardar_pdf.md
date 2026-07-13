---
id: "certificados.certificado_emitido_guardar_pdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Guardar Pdf"
capacidad: "certificados.certificado_emitido_guardar_pdf.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_2_mpdf"]
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_emitido_guardar_pdf"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Guardar Pdf

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Persistir el PDF generado y el número de certificado en BD.

## Punto De Entrada

Tras previsualizar en mPDF (`certificado_emitido_2_mpdf`).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_2_mpdf`

## Escenarios Inferidos

### Ejecutar

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

- `/src/certificados/certificado_emitido_guardar_pdf`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
