---
id: "procesos.procesos_ver_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_ver_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_ver_data.php"
entrada: ["post.id_item:integer", "post.mod:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosVerData"]
tags: ["procesos", "ver", "data"]
estado_revision: "generado"
---

# Procesos Ver Data

Caso de uso: datos para la pantalla `procesos_ver`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_ver_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_ver_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | No | controller |
| `mod` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\procesos\application\ProcesosVerData`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.