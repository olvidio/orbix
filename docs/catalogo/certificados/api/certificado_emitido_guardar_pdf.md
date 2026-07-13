---
id: "certificados.certificado_emitido_guardar_pdf"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_guardar_pdf"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar_pdf.php"
entrada: ["post.certificado:string", "post.id_item:integer", "post.id_nom:integer", "post.pdf:string"]
entrada_obligatoria: ["id_item", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_2_mpdf.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoGuardarMessages"]
tags: ["certificados", "certificado", "emitido", "guardar", "pdf"]
estado_revision: "revisado"
---

# Certificado Emitido Guardar Pdf

Persiste el PDF generado y registra el número de certificado en notas de otras regiones.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Decodifica `pdf` y `certificado` (base64), guarda el documento en el registro emitido y llama a
`PersonaNotaOtraRegionStgrRepository::addCertificado` con el esquema de sesión STGR.

## Endpoint

- URL: `/src/certificados/certificado_emitido_guardar_pdf`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar_pdf.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | Sí | Certificado emitido |
| `id_nom` | `integer` | controller | Sí | Persona |
| `pdf` | `string` | controller | No | Contenido PDF en base64 |
| `certificado` | `string` | controller | No | Número certificado en base64 |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `data` = `{mensaje: "ok", item: <id_item>}` (doble `JSON.parse`)

## Errores conocidos

- `No encuentro certificado emitido con id_item: %d`
- Mensajes de `CertificadoEmitidoGuardarMessages` (duplicado, BD)
- Errores al actualizar notas otras regiones

## Permisos

- Sin control de permisos propio; invocado tras generar PDF en región STGR.

## Casos De Uso

- `src\certificados\application\CertificadoEmitidoGuardarMessages`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_2_mpdf.php`
