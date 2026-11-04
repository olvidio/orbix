---
id: "certificados.certificado_emitido_upload_firmado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Subir el certificado firmado"
capacidad: "certificados.certificado_emitido_upload_firmado.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_upload_firmado"]
fragmentos: ["certificados.pantalla.certificado_emitido_upload_firmado"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_upload_firmado_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Upload Firmado

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Subir el PDF firmado de un certificado ya emitido.

## Punto De Entrada

Menú Pills2 **ESTUDIOS > Actas y certificados > Certificados**.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_upload_firmado`

## Escenarios Inferidos

### Subir el certificado firmado

Pasos propuestos:
1. Imprimir y firmar el certificado.
2. Abrir el certificado en la lista y elegir el PDF firmado.
3. Subirlo; el certificado queda con su versión firmada.

Endpoints asociados:
- `/src/certificados/certificado_emitido_upload_firmado_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_upload_firmado_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
