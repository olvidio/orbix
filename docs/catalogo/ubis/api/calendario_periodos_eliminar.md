---
id: "ubis.calendario_periodos_eliminar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borar", "no se encuentra el periodo a borrar", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/ubis/controller/calendario_periodos.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodoEliminar"]
tags: ["ubis", "calendario", "periodos", "eliminar"]
estado_revision: "revisado"
---

# Calendario Periodos Eliminar

Elimina un periodo de calendario CDC identificado por id_item.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina un periodo de calendario CDC identificado por id_item.

## Endpoint

- URL: `/src/ubis/calendario_periodos_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `no sé cuál he de borar`
- `no se encuentra el periodo a borrar`
- `hay un error, no se ha eliminado`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `UbiPermisos` (`puedeModificarPorObjeto`, `dlPerteneceAMiDelegacion`), `have_perm_oficina(scdl|scl|vcsd|des|admin_sv)` y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\ubis\application\CalendarioPeriodoEliminar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/calendario_periodos.php"]`).
