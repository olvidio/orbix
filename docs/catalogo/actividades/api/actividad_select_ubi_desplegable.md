---
id: "actividades.actividad_select_ubi_desplegable"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_select_ubi_desplegable"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_select_ubi_desplegable.php"
entrada: ["post.tipo:string", "post.dl_org:string", "post.isfsv:integer"]
entrada_obligatoria: ["tipo"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadSelectUbiDesplegableData"
respuesta_data: ["id:string", "opciones:array", "selected:string", "action:string", "mensaje:string", "blanco:bool", "val_blanco:string"]
requiere_hashb: false
errores: ["opción no definida: tipo=<valor>", "falta saber quien organiza"]
frontend_referencias: ["frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/view/actividad_select_ubi.phtml"]
casos_uso: ["src\\actividades\\application\\ActividadSelectUbiData"]
tags: ["actividades", "actividad", "select", "ubi", "desplegable"]
estado_revision: "revisado"
---

# Actividad Select Ubi Desplegable

Devuelve el payload de un desplegable (value → label) para la pantalla "seleccionar lugar para una
actividad". El frontend construye el `<select>` a partir del array devuelto.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Según `tipo` devuelve uno de dos desplegables (el caso de uso `ActividadSelectUbiData` calcula ambos
conjuntos de opciones, pero el controller emite solo el pedido):

- `tipo=freq`: casas frecuentes de la delegación organizadora (`id_ubi_1`). Requiere `dl_org`; si falta,
  responde con `mensaje = "falta saber quien organiza"` y sin opciones.
- `tipo=region`: delegaciones + regiones activas (`filtro_lugar`), con `action = fnjs_lugar()` y, si hay
  `dl_org`, preselecciona `dl|<dl_org>`.

Un `tipo` distinto de `freq`/`region` devuelve error `success: false`.

## Endpoint

- URL: `/src/actividades/actividad_select_ubi_desplegable`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_select_ubi_desplegable.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller | Sí | `freq` o `region` |
| `dl_org` | `string` | controller | No | Delegación organizadora (obligatoria de hecho para `freq`) |
| `isfsv` | `integer` | controller | No | 1 = solo sv, 2 = solo sf, otro = sin filtro |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data`: `{ id, opciones, selected, action, mensaje, blanco, val_blanco }`.
- En `tipo` no soportado: `success: false`, `mensaje: "opción no definida: tipo=<valor>"`.

## Errores conocidos

- `opción no definida: tipo=<valor>` (`success: false`)
- `falta saber quien organiza` (devuelto en `data.mensaje` cuando `tipo=freq` sin `dl_org`)

## Permisos

- Sin control de permisos propio. La autorización se resuelve en el frontend
  (`actividad_select_ubi.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\ActividadSelectUbiData`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_select_ubi.php`
- `frontend/actividades/view/actividad_select_ubi.phtml`
