---
id: "misas.nuevo_status"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/nuevo_status"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/nuevo_status.php"
entrada: ["post.id_zona:integer", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string", "post.estado:integer"]
entrada_obligatoria: ["id_zona", "periodo", "estado"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_NuevoStatusPeriodoData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\NuevoStatusPeriodo"]
tags: ["misas", "nuevo", "status"]
estado_revision: "revisado"
errores: ["<repositorio getErrorTxt() acumulado>"]
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
| `id_zona` | `integer` | application | Si | |
| `periodo` | `string` | application | Si | |
| `empiezamin` | `string` | application | No | |
| `empiezamax` | `string` | application | No | |
| `estado` | `integer` | application | Si | |

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
