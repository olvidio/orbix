---
id: "asistentes.asistente_plaza_asignar"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/asistente_plaza_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/asistente_plaza_asignar.php"
entrada: ["post.id_activ:integer", "post.lista_json:string", "post.plaza:mixed"]
entrada_obligatoria: ["id_activ", "lista_json"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ", "falta lista de seleccion", "no se encuentra el asistente (id_nom: %s, id_activ: %s)", "los datos de asistencia los modifica la dl del asistente", "hay un error, no se ha guardado"]
frontend_referencias: []
casos_uso: ["src\\asistentes\\application\\AsistentePlazaAsignar"]
tags: ["asistentes", "asistente", "plaza", "asignar"]
estado_revision: "revisado"
---

# Asistente Plaza Asignar

Asigna plaza común a un lote de asistentes seleccionados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Case `plaza` del legacy `update_3101.php`: recibe `lista_json` con objetos `{value: "id_nom#..."}` y
aplica la misma `plaza` a cada asistente de la actividad. Si `plaza` viene vacía, la deja en `null`.

## Endpoint

- URL: `/src/asistentes/asistente_plaza_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/asistente_plaza_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | Actividad común del lote |
| `lista_json` | `string` | application | Si | JSON array de objetos con `value` = `id_nom#...` |
| `plaza` | `mixed` | application | No | Entero o vacío (`null` en entidad) |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`. Errores parciales se concatenan en el string de `data`.

## Errores conocidos

- `falta id_activ`
- `falta lista de seleccion`
- `no se encuentra el asistente (id_nom: %s, id_activ: %s)` (por fila)
- `los datos de asistencia los modifica la dl del asistente` (por fila)
- Errores de `setPlazaComprobando` (por fila)
- `hay un error, no se ha guardado` (por fila)

## Permisos

- Por asistente: `perm_modificar()` en el caso de uso.
- Lote desde listado de actividad: autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\AsistentePlazaAsignar`

## Frontend Relacionado

- Invocado desde el listado de asistentes de una actividad (asignación masiva de plaza). No hay
  referencia literal a la URL en `frontend/`.
