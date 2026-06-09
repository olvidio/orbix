---
id: "actividadestudios.acta_notas_definitivas_grabar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/acta_notas_definitivas_grabar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_definitivas_grabar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer"]
entrada_obligatoria: []
respuesta: "raw_response"
respuesta_data_schema: "actividadestudios_ActaNotasDefinitivasGrabarData"
respuesta_data: ["success:bool, mensaje: string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasDefinitivasGrabar"]
tags: ["actividadestudios", "acta", "notas", "definitivas", "grabar"]
estado_revision: "generado"
---

# Acta Notas Definitivas Grabar

Convierte las matriculas/notas borrador en `PersonaNota` definitivas (rama `que=3` del legacy `apps/actividadestudios/controller/acta_notas_update.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/acta_notas_definitivas_grabar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_definitivas_grabar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | No | application |
| `id_asignatura` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_ActaNotasDefinitivasGrabarData`):
  - `success` (`bool, mensaje: string`)

## Casos De Uso

- `src\actividadestudios\application\ActaNotasDefinitivasGrabar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/acta_notas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.