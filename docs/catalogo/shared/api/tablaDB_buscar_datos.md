---
id: "shared.tablaDB_buscar_datos"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_buscar_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos.php"
entrada: ["post.aSerieBuscar:string", "post.clase_info:string", "post.id_pau:integer", "post.k_buscar:string", "post.obj_pau:string", "post.pau:string"]
entrada_obligatoria: ["clase_info"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/controller/tablaDB_lista_ver.php"]
casos_uso: []
tags: ["shared", "tablaDB", "buscar", "datos"]
estado_revision: "revisado"
---

# TablaDB Buscar Datos

Builder del formulario de búsqueda previo al listado `tablaDB`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Primera fase de `tablaDB_lista_ver.php`: cuando no hay `k_buscar` ni `aSerieBuscar`, obtiene campos
de filtro (`addCamposFormBuscar`), textos y vista opcional personalizada antes de cargar la tabla.

## Endpoint

- URL: `/src/shared/tablaDB_buscar_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | controller | Sí | Clase `Info*` URL-encoded. |
| `k_buscar` | `string` | controller | No | Suele ir vacío en la primera carga. |
| `aSerieBuscar` | `string` | controller | No | Serie de criterios serializada (segunda pasada). |
| `pau` | `string` | controller | No | Contexto padre. |
| `id_pau` | `integer` | controller | No | Id padre. |
| `obj_pau` | `string` | controller | No | Objeto auxiliar. |

## Salida

- Helper: `ContestarJson::enviar`
- Payload (doble `JSON.parse`):

| Clave | Descripción |
|-------|-------------|
| `a_campos` | Metadatos del buscador: `script`, `txt_buscar`, `k_buscar`, `camposForm`, campos extra del `Info*`. |
| `buscar_view` | Vista `.phtml` alternativa (si `getBuscar_view()` del `Info*`). |
| `namespace_view` | Namespace de la vista (default `frontend\shared\view`). |

Si no hay `buscar_view`, el frontend usa `tablaDB_busqueda.phtml`.

## Errores conocidos

- Sin mensajes `_()` en el controller.

## Permisos

- Sin control en el endpoint.

## Casos De Uso

Lógica inline vía `DatosInfoRepo` + `DatosTablaRepo`.

## Frontend Relacionado

- Primera fase de `frontend/shared/controller/tablaDB_lista_ver.php`.
