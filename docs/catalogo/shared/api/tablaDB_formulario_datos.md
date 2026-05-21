---
id: "shared.tablaDB_formulario_datos"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_formulario_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_formulario_datos.php"
entrada: ["post.a_pkey:mixed", "post.clase_info:mixed", "post.mod:mixed", "post.obj_pau:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/controller/tablaDB_formulario_ver.php"]
casos_uso: []
tags: ["shared", "tablaDB", "formulario", "datos"]
estado_revision: "generado"
---

# TablaDB Formulario Datos

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/shared/tablaDB_formulario_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_formulario_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `a_pkey` | `mixed` | controller | No | controller |
| `clase_info` | `mixed` | controller | No | controller |
| `mod` | `mixed` | controller | No | controller |
| `obj_pau` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/shared/controller/tablaDB_formulario_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.