---
id: "actividadestudios.asistente_plan_est_ok"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/asistente_plan_est_ok"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/asistente_plan_est_ok.php"
entrada: ["post.est_ok:string", "post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ o id_nom", "no encuentro al asistente", "hay un error, no se ha guardado"]
frontend_referencias: []
casos_uso: ["src\\actividadestudios\\application\\AsistentePlanEstOk"]
tags: ["actividadestudios", "asistente", "plan", "est", "ok"]
estado_revision: "generado"
---

# Asistente Plan Est Ok

Marca el flag `est_ok` (plan de estudios confirmado) de un Asistente. Sustituye al case `plan` de `update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/asistente_plan_est_ok`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_plan_est_ok.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `est_ok` | `string` | application | No | application |
| `id_activ` | `integer` | application | No | application |
| `id_nom` | `integer` | application | No | application |
| `id_pau` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Marca el flag `est_ok` (plan de estudios confirmado) de un Asistente.

## Errores conocidos

- `falta id_activ o id_nom`
- `no encuentro al asistente`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\actividadestudios\application\AsistentePlanEstOk`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.