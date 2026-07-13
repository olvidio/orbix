---
id: "actividades.tipo_activ_eliminar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_eliminar.php"
entrada: ["post.id_tipo_activ:integer"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["tipo de actividad no encontrado", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivEliminar"]
tags: ["actividades", "tipo", "activ", "eliminar"]
estado_revision: "revisado"
---

# Tipo Activ Eliminar

Elimina un tipo de actividad. Portado del case `eliminar` del dispatcher legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Busca el `TipoDeActividad` por `id_tipo_activ` y lo elimina. Al eliminar invalida la caché de sesión
(`TipoActivMetadataLoader::forget()`). Devuelve mensaje vacío en éxito. El frontend pide confirmación
("¿Está seguro que quiere eliminar este tipo de actividad?") antes de invocarlo.

## Endpoint

- URL: `/src/actividades/tipo_activ_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `integer` | application | Sí | Tipo a eliminar |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con la clave `mensaje`: cadena **vacía** en éxito, o el texto de error. Los
  errores de negocio viajan dentro de `data.mensaje`, no como `success: false`.

## Errores conocidos

- `tipo de actividad no encontrado`
- `hay un error, no se ha eliminado`

## Permisos

- Sin control de permisos propio. La autorización se resuelve en el frontend (`tipo_activ.php`, firma
  `HashFront`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\TipoActivEliminar`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php` (emite la URL como `url_eliminar`).
