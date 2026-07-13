---
id: "misas.quitar_horario"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/quitar_horario"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/quitar_horario.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_QuitarHorarioPlantillaData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/horario_tarea.php"]
casos_uso: ["src\\misas\\application\\QuitarHorarioPlantilla"]
tags: ["misas", "quitar", "horario"]
estado_revision: "revisado"
errores: ["Error: falta el id_item", "No se encuentra la plantilla %d", "<repositorio getErrorTxt()>"]
---

# Quitar horario

Anula t_start/t_end de una fila Plantilla (quita horario asignado a la tarea).

Linaje: Slice 9 — migrado desde apps/misas/controller/quitar_horario.php (zquitar_horario es alias).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Anula t_start/t_end de una fila Plantilla (quita horario asignado a la tarea).

## Endpoint

- URL: `/src/misas/quitar_horario`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/quitar_horario.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacio serializado).

## Errores conocidos
- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\QuitarHorarioPlantilla`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/horario_tarea.php"]`).
