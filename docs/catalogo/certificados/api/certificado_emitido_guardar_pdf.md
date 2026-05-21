---
id: "certificados.certificado_emitido_guardar_pdf"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_guardar_pdf"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar_pdf.php"
entrada: ["post.certificado:string", "post.id_item:integer", "post.id_nom:integer", "post.pdf:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_2_mpdf.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoGuardarMessages"]
tags: ["certificados", "certificado", "emitido", "guardar", "pdf"]
estado_revision: "generado"
---

# Certificado Emitido Guardar Pdf

Mensajes legibles al guardar un certificado emitido (errores de BD, etc.).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_emitido_guardar_pdf`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar_pdf.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `certificado` | `string` | controller | No | controller |
| `id_item` | `integer` | controller | No | controller |
| `id_nom` | `integer` | controller | No | controller |
| `pdf` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\certificados\application\CertificadoEmitidoGuardarMessages`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_2_mpdf.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.