---
id: "asistentes.asistente_plaza_asignar"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/asistente_plaza_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/asistente_plaza_asignar.php"
entrada: ["post.id_activ:integer", "post.lista_json:string", "post.plaza:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ", "falta lista de seleccion"]
frontend_referencias: []
casos_uso: ["src\\asistentes\\application\\AsistentePlazaAsignar"]
tags: ["asistentes", "asistente", "plaza", "asignar"]
estado_revision: "generado"
---

# Asistente Plaza Asignar

Cambia la plaza de un lote de asistentes.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/asistente_plaza_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_plaza_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `lista_json` | `string` | application | No | application |
| `plaza` | `mixed` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Cambia la plaza asignada de un lote de asistentes (columna `plaza`).
- Sustituye al case `plaza` del antiguo `apps/asistentes/controller/update_3101.php`, que recibia un `lista_json` con los asistentes seleccionados y un `plaza` comun.

## Errores conocidos

- `falta id_activ`
- `falta lista de seleccion`

## Casos De Uso

- `src\asistentes\application\AsistentePlazaAsignar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.