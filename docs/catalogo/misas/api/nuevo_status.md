---
id: "misas.nuevo_status"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/nuevo_status"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/nuevo_status.php"
entrada: ["post.ctx_nuevo_status:string", "post.id_zona:integer", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string", "post.estado:integer"]
entrada_obligatoria: ["ctx_nuevo_status", "periodo", "estado"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_NuevoStatusPeriodoData"
respuesta_data: ["error:string"]
requiere_hashb: true
hashb_campo: "ctx_nuevo_status"
hashb_action: "nuevo_status"
frontend_referencias: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\NuevoStatusPeriodo"]
tags: ["misas", "nuevo", "status"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "<repositorio getErrorTxt() acumulado>"]
---

# Nuevo status

Actualiza masivamente el status de todos los EncargoDia de encargos 8100+ de una zona en el rango de fechas indicado.

Linaje: Slice 10 — migrado desde apps/misas/controller/nuevo_status.php (antes respuesta HTML vacía).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Actualiza masivamente el status de todos los EncargoDia de encargos 8100+ de una zona en el rango de fechas indicado.

## Endpoint

- URL: `/src/misas/nuevo_status`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/nuevo_status.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx_nuevo_status` | `string` | cápsula HashB | Sí | Cápsula firmada (acción `nuevo_status`). Los campos que vayan dentro de la cápsula se toman de ella y se ignoran en el body. |
| `id_zona` | `integer` | application | No | Se toma de la cápsula HashB; si viene en el body se ignora. |
| `periodo` | `string` | application | Si | |
| `empiezamin` | `string` | application | No | |
| `empiezamax` | `string` | application | No | |
| `estado` | `integer` | application | Si | |

## Autorizacion HashB

- Campo POST: `ctx_nuevo_status`
- Accion: `nuevo_status`
- Contexto firmado: `{id_zona}`
- Origen de la cápsula: `cambiar_status_data` → `zona_ctx_map[<id_zona>]` (una cápsula por zona)
- Cápsula inválida: `success: false`, `mensaje: "Operación no autorizada"`, `data: "none"`.
- Ver `docs/dev/hash_arquitectura.md`.

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "{}"`.

## Errores conocidos
- `<repositorio getErrorTxt() acumulado>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\NuevoStatusPeriodo`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/cambiar_status.php"]`).
