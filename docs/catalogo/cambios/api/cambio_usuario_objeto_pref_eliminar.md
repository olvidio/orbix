---
id: "cambios.cambio_usuario_objeto_pref_eliminar"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_objeto_pref_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_eliminar.php"
entrada: ["post.id_item_usuario_objeto:integer", "post.sel:array"]
entrada_obligatoria: ["id_item_usuario_objeto"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_form_avisos.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefEliminar"]
tags: ["cambios", "cambio", "usuario", "objeto", "pref", "eliminar"]
estado_revision: "revisado"
---

# Cambio Usuario Objeto Pref Eliminar

Elimina un `CambioUsuarioObjetoPref` (preferencia de aviso sobre un objeto/tipo de actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la preferencia indicada por `id_item_usuario_objeto`. Si llega `sel[]`, extrae el id del
segundo segmento del primer token (`id_usuario#id_item_usuario_objeto`).

## Endpoint

- URL: `/src/cambios/cambio_usuario_objeto_pref_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_objeto_pref_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_usuario_objeto` | `integer` | controller+application | Sí | Directo o vía `sel[0]` (`#` separador) |
| `sel` | `array` | controller | No | Alternativa: `id_usuario#id_item_usuario_objeto` |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: []` (array vacío).
- Error: mensaje en el envelope.

## Errores conocidos

- `falta id_item_usuario_objeto`
- `preferencia no encontrada`
- `Hay un error, no se ha eliminado`

## Permisos

- Sin control propio; autorización en `usuario_form_avisos` + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioObjetoPrefEliminar`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_form_avisos.php`: `fnjs_del_cambio` redirige el formulario a
  este endpoint con `sel` de la fila seleccionada.
