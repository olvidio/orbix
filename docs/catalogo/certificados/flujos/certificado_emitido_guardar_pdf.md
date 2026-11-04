---
id: "certificados.certificado_emitido_guardar_pdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Guardar el certificado generado"
capacidad: "certificados.certificado_emitido_guardar_pdf.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_2_mpdf"]
fragmentos: ["certificados.pantalla.certificado_emitido_2_mpdf"]
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_emitido_guardar_pdf"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Guardar Pdf

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Guardar en el sistema el PDF generado y el número del certificado.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_2_mpdf`

## Escenarios Inferidos

### Guardar el certificado generado

Pasos propuestos:
1. Tras generar el PDF, confirmar el guardado.
2. El certificado queda registrado con su número y su PDF.

Endpoints asociados:
- `/src/certificados/certificado_emitido_guardar_pdf`

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
