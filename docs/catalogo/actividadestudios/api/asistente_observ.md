---
id: "actividadestudios.asistente_observ"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/asistente_observ"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.observ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ o id_nom", "no encuentro al asistente", "hay un error, no se ha guardado"]
frontend_referencias: []
casos_uso: ["src\\actividadestudios\\application\\AsistenteObserv"]
tags: ["actividadestudios", "asistente", "observ"]
estado_revision: "generado"
---

# Asistente Observ

Guarda el texto `observ` de un Asistente. Sustituye al case `observ` de `update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/asistente_observ`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |
| `observ` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Guarda el texto `observ` de un Asistente.

## Errores conocidos

- `falta id_activ o id_nom`
- `no encuentro al asistente`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadestudios\application\AsistenteObserv`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.