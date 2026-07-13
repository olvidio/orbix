---
id: "certificados.certificado_emitido_upload_firmado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Upload Firmado"
capacidad: "certificados.certificado_emitido_upload_firmado.gestionar"
pantallas_principales: []
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

Listado Certificados → subir pdf firmado.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_upload_firmado`

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

- `/src/certificados/certificado_emitido_upload_firmado_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
