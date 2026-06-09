---
id: "inventario.documentos_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/documentos_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/documentos_guardar.php"
entrada: ["post.chk_eliminado:string", "post.chk_f_asignado:string", "post.chk_f_eliminado:string", "post.chk_f_recibido:string", "post.chk_num_fin:string", "post.chk_num_ini:string", "post.documentos:string", "post.eliminado:integer", "post.f_asignado:string", "post.f_eliminado:string", "post.f_recibido:string", "post.num_fin:string", "post.num_ini:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["inventario", "documentos", "guardar"]
estado_revision: "generado"
---

# Documentos Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/documentos_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/documentos_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `chk_eliminado` | `string` | controller | No | controller |
| `chk_f_asignado` | `string` | controller | No | controller |
| `chk_f_eliminado` | `string` | controller | No | controller |
| `chk_f_recibido` | `string` | controller | No | controller |
| `chk_num_fin` | `string` | controller | No | controller |
| `chk_num_ini` | `string` | controller | No | controller |
| `documentos` | `string` | controller | No | controller |
| `eliminado` | `integer` | controller | No | controller |
| `f_asignado` | `string` | controller | No | controller |
| `f_eliminado` | `string` | controller | No | controller |
| `f_recibido` | `string` | controller | No | controller |
| `num_fin` | `string` | controller | No | controller |
| `num_ini` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.