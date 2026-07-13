---
id: "notas.acta_eliminar"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_eliminar.php"
entrada: ["post.acta:string", "post.sel:array"]
entrada_obligatoria: ["acta"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el acta", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/notas/controller/acta_select.php"]
casos_uso: ["src\\notas\\application\\ActaEliminar"]
tags: ["notas", "acta", "eliminar"]
estado_revision: "revisado"
---

# Acta Eliminar

Elimina un acta y su tribunal asociado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.

## Objetivo funcional

Borrado desde listado `acta_select` (`fnjs_eliminar`) o contextos que reutilicen el endpoint.

## Permisos

- `have_perm_oficina('est')` en `acta_select` (DL).

## Errores conocidos

- `No se encuentra el acta`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\notas\application\ActaEliminar`

## Frontend Relacionado

- `frontend/notas/controller/acta_select.php`.