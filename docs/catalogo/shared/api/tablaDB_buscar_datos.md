---
id: "shared.tablaDB_buscar_datos"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_buscar_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos.php"
entrada: ["post.aSerieBuscar:string", "post.clase_info:string", "post.id_pau:integer", "post.k_buscar:string", "post.obj_pau:string", "post.pau:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/controller/tablaDB_lista_ver.php"]
casos_uso: []
tags: ["shared", "tablaDB", "buscar", "datos"]
estado_revision: "generado"
---

# TablaDB Buscar Datos

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/shared/tablaDB_buscar_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `aSerieBuscar` | `string` | controller | No | controller |
| `clase_info` | `string` | controller | No | controller |
| `id_pau` | `integer` | controller | No | controller |
| `k_buscar` | `string` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |
| `pau` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/shared/controller/tablaDB_lista_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.