---
id: "shared.tablaDB_lista_datos"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_lista_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_lista_datos.php"
entrada: ["post.clase_info:string", "post.id_pau:integer", "post.k_buscar:string", "post.obj_pau:integer", "post.pau:string"]
entrada_obligatoria: ["clase_info"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/controller/tablaDB_formulario_ver.php", "frontend/shared/controller/tablaDB_lista_ver.php"]
casos_uso: []
tags: ["shared", "tablaDB", "lista", "datos"]
estado_revision: "revisado"
---

# TablaDB Lista Datos

Builder de tabla editable para el patrón genérico `tablaDB`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve `Info*` desde `clase_info`, aplica filtros de contexto (`pau`, `id_pau`, `obj_pau`,
`k_buscar`) y devuelve cabeceras, filas, botones y metadatos para renderizar `Lista` en
`tablaDB_lista_ver.php`.

## Endpoint

- URL: `/src/shared/tablaDB_lista_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_lista_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | controller | Sí | Clase `Info*` URL-encoded. |
| `k_buscar` | `string` | controller | No | Criterio de búsqueda (tras formulario buscar). |
| `pau` | `string` | controller | No | Tipo de objeto padre en navegación. |
| `id_pau` | `integer` | controller | No | Id del objeto padre. |
| `obj_pau` | `integer` | controller | No | Contexto dossier / objeto auxiliar. |

## Salida

- Helper: `ContestarJson::enviar`
- Payload (doble `JSON.parse` en cliente habitual):

| Clave | Descripción |
|-------|-------------|
| `a_cabeceras` | Cabeceras de columna para `Lista`. |
| `a_botones` | Botones de fila (editar, etc.). |
| `a_valores` | Filas de datos. |
| `id_tabla` | Id DOM (`repo_tabla_sql_{clase}`). |
| `script` | JS adicional de la tabla. |
| `titulo` | Título de pantalla. |
| `explicacion` | Texto explicativo. |

## Errores conocidos

- Sin mensajes `_()` en el controller; errores de colección/repositorio pueden propagarse como
  excepción no capturada.

## Permisos

- Sin control en el endpoint; permisos y visibilidad del botón «nuevo» en frontend (`permiso`).

## Casos De Uso

Lógica inline vía `DatosTablaRepo` + `DatosInfoRepoResolver`.

## Frontend Relacionado

- Segunda fase de `frontend/shared/controller/tablaDB_lista_ver.php` (cuando hay `k_buscar` o
  `aSerieBuscar`).
- `go_to` de retorno en `tablaDB_formulario_ver.php` apunta aquí vía query firmada (no es invocación
  AJAX típica).
