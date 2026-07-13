---
id: "actividades.tipo_activ_update"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_update.php"
entrada: ["post.id_tipo_activ:integer", "post.nom_tipo_activ:string"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["tipo de actividad no encontrado", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivUpdate"]
tags: ["actividades", "tipo", "activ", "update"]
estado_revision: "revisado"
---

# Tipo Activ Update

Actualiza el nombre de un tipo de actividad existente. Portado del case `update` del dispatcher legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Busca el `TipoDeActividad` por `id_tipo_activ`, actualiza su `nombre` con `nom_tipo_activ` y guarda.
Al guardar invalida la caché de sesión (`TipoActivMetadataLoader::forget()`). Devuelve mensaje vacío
en éxito.

## Endpoint

- URL: `/src/actividades/tipo_activ_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `integer` | application | Sí | Tipo a modificar |
| `nom_tipo_activ` | `string` | application | No | Nuevo nombre |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- `data` es un objeto con la clave `mensaje`: cadena **vacía** en éxito, o el texto de error. Los
  errores de negocio viajan dentro de `data.mensaje`, no como `success: false`.

## Errores conocidos

- `tipo de actividad no encontrado`
- `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio. La autorización se resuelve en el frontend (`tipo_activ.php`, firma
  `HashFront` sobre `id_tipo_activ`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\TipoActivUpdate`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php` (emite la URL como `url_update`).
