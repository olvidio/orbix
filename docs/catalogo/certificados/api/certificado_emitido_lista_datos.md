---
id: "certificados.certificado_emitido_lista_datos"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_lista_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_lista_datos.php"
entrada: ["post.certificado:string", "post.fincurs_ca_iso:string", "post.inicurs_ca_iso:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_lista.php"]
casos_uso: []
tags: ["certificados", "certificado", "emitido", "lista", "datos"]
estado_revision: "generado"
---

# Certificado Emitido Lista Datos

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/certificados/certificado_emitido_lista_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_lista_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `certificado` | `string` | controller | No | controller |
| `fincurs_ca_iso` | `string` | controller | No | controller |
| `inicurs_ca_iso` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.