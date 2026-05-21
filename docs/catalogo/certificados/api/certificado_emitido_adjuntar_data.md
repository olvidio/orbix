---
id: "certificados.certificado_emitido_adjuntar_data"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_adjuntar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_adjuntar_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "certificados_CertificadoEmitidoAdjuntarFormDataData"
respuesta_data: ["nom:string, f_enviado: string"]
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_adjuntar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoAdjuntarFormData"]
tags: ["certificados", "certificado", "emitido", "adjuntar", "data"]
estado_revision: "generado"
---

# Certificado Emitido Adjuntar Data

Datos para el formulario “adjuntar certificado emitido” (solo lectura inicial).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_emitido_adjuntar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_adjuntar_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `certificados_CertificadoEmitidoAdjuntarFormDataData`):
  - `nom` (`string, f_enviado: string`)

## Casos De Uso

- `src\certificados\application\CertificadoEmitidoAdjuntarFormData`

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_adjuntar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.