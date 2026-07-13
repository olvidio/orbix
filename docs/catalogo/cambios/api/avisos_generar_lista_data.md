---
id: "cambios.avisos_generar_lista_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/avisos_generar_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/cambios/infrastructure/ui/http/controllers/avisos_generar_lista_data.php"
entrada: ["post.aviso_tipo:integer", "post.id_usuario:integer", "post.is_admin:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/avisos_generar.php"]
casos_uso: ["src\\cambios\\application\\AvisosGenerarListaData"]
tags: ["cambios", "avisos", "generar", "lista", "data"]
estado_revision: "revisado"
---

# Avisos Generar Lista Data

Listado de `CambioUsuario` pendientes de avisar (`avisado=false`) para el usuario y tipo de aviso
indicados, más opciones de desplegables y metadatos de borrado de la pantalla `avisos_generar`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye la tabla de cambios anotados no avisados. Si `is_admin=0`, ignora `id_usuario`/`aviso_tipo`
del POST y usa `ConfigGlobal::mi_id_usuario()` y `AvisoTipoId::TIPO_LISTA`. Si `is_admin=1`, toma
los valores del formulario. Con `id_usuario=0` devuelve solo los desplegables vacíos. Con usuario
válido añade `paths` y `hash_*` para que `AvisosGenerarListaRender` firme las URLs de eliminación.

## Endpoint

- URL: `/src/cambios/avisos_generar_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/avisos_generar_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `is_admin` | `integer` | controller | No | `1` = admin elige usuario/tipo; `0` = sesión |
| `id_usuario` | `integer` | controller+application | No | Solo efectivo si `is_admin=1` |
| `aviso_tipo` | `integer` | controller+application | No | Solo efectivo si `is_admin=1` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en frontend).
- Payload en `data`:
  - `error` (`string`): vacío en éxito.
  - `a_valores` (`array`): filas ordenadas por timestamp; cada fila tiene `sel` =
    `id_item_cambio#id_usuario#sfsv#aviso_tipo`, `1` = fecha local, `2` = quien cambió, `3` = texto
    del aviso.
  - `aOpcionesUsuarios`, `aOpcionesAvisoTipo` (`array`): opciones de desplegables.
  - `effective_id_usuario`, `effective_aviso_tipo` (`int`): valores aplicados.
  - Si `effective_id_usuario > 0`: `paths` (`eliminar`, `eliminar_fecha`), `hash_eliminar`
    (`campos_no: sel`), `hash_eliminar_fecha` (`campos_form: f_fin`).

## Permisos

- Sin control propio; `is_admin` lo calcula `CambiosPermSupport::isAdmin()` en el frontend.

## Casos De Uso

- `src\cambios\application\AvisosGenerarListaData`

## Frontend Relacionado

- `frontend/cambios/controller/avisos_generar.php`: carga el listado vía `PostRequest::getDataFromUrl`;
  `AvisosGenerarListaRender::enrich` firma URLs de borrado con `HashFront`.
