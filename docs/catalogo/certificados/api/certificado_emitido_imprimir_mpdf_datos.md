---
id: "certificados.certificado_emitido_imprimir_mpdf_datos"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_imprimir_mpdf_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_mpdf_datos.php"
entrada: ["post.id_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_imprimir_mpdf.php"]
casos_uso: []
tags: ["certificados", "certificado", "emitido", "imprimir", "mpdf", "datos"]
estado_revision: "generado"
---

# Certificado Emitido Imprimir Mpdf Datos

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_emitido_imprimir_mpdf_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_mpdf_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_imprimir_mpdf.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.