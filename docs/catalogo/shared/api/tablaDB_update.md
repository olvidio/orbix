---
id: "shared.tablaDB_update"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_update.php"
entrada: ["post.clase_info:string", "post.go_to:string", "post.id_pau:string", "post.mod:string", "post.obj_pau:string", "post.s_pkey:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/view/tablaDB_formulario.phtml"]
casos_uso: []
tags: ["shared", "tablaDB", "update"]
estado_revision: "generado"
---

# TablaDB Update

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/shared/tablaDB_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | controller | No | controller |
| `go_to` | `string` | controller | No | controller |
| `id_pau` | `string` | controller | No | controller |
| `mod` | `string` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |
| `s_pkey` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/shared/view/tablaDB_formulario.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.