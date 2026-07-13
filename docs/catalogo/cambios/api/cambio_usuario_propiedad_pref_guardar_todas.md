---
id: "cambios.cambio_usuario_propiedad_pref_guardar_todas"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/cambio_usuario_propiedad_pref_guardar_todas"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_guardar_todas.php"
entrada: ["post.id_item_usuario_objeto_prop:integer", "post.objeto_prop:string"]
entrada_obligatoria: ["id_item_usuario_objeto_prop", "objeto_prop"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioPropiedadPrefGuardarTodas"]
tags: ["cambios", "cambio", "usuario", "propiedad", "pref", "guardar", "todas"]
estado_revision: "revisado"
---

# Cambio Usuario Propiedad Pref Guardar Todas

Sincroniza las `CambioUsuarioPropiedadPref` de un `CambioUsuarioObjetoPref`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe el POST completo del formulario de propiedades. Itera `$objeto[]` (checkboxes marcados), crea o
actualiza cada `CambioUsuarioPropiedadPref` (con condición JSON en campos dinámicos `{objeto}_{prop}_cond`)
y elimina las propiedades desmarcadas.

## Endpoint

- URL: `/src/cambios/cambio_usuario_propiedad_pref_guardar_todas`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cambios/infrastructure/ui/http/controllers/cambio_usuario_propiedad_pref_guardar_todas.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_usuario_objeto_prop` | `integer` | application | Sí | FK al objeto-pref |
| `objeto_prop` | `string` | application | Sí | Nombre del objeto (clave del array POST) |
| `{objeto}[]` | `array` | application | No | IDs de condición seleccionados |
| `{objeto}_{prop}_cond` | `string` | application | No | JSON de condición (`cambio_prop`) |
| `{objeto}_{prop}_item` | `integer` | application | No | `id_item` existente |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `success: true`, `data: []`.
- Error: mensaje en envelope.

## Errores conocidos

- `faltan parametros`
- `Hay un error, no se ha guardado`
- `Hay un error, no se ha eliminado`

## Permisos

- Sin control propio; segundo paso de `fnjs_grabar_todo` en `usuario_avisos_pref`.

## Casos De Uso

- `src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref.php`: envía el formulario completo tras guardar el
  objeto-pref (`url_guardar_propiedades`).
