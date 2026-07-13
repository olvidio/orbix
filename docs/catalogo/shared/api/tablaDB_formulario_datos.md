---
id: "shared.tablaDB_formulario_datos"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_formulario_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_formulario_datos.php"
entrada: ["post.a_pkey:mixed", "post.clase_info:string", "post.mod:string", "post.obj_pau:mixed"]
entrada_obligatoria: ["clase_info", "mod"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/controller/tablaDB_formulario_ver.php"]
casos_uso: []
tags: ["shared", "tablaDB", "formulario", "datos"]
estado_revision: "revisado"
---

# TablaDB Formulario Datos

Builder del formulario de alta/edición genérico `tablaDB`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga la ficha (`getFicha`) según `mod` (`nuevo` / `editar` / `eliminar`) y `a_pkey`, precalcula
opciones de campos dependientes (`getArrayCamposDepende`) y devuelve la definición de campos para
`tablaDB_formulario.phtml`.

## Endpoint

- URL: `/src/shared/tablaDB_formulario_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_formulario_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | controller | Sí | Clase `Info*` URL-encoded. |
| `mod` | `string` | controller | Sí | `nuevo`, `editar` o vacío (lectura). |
| `a_pkey` | `mixed` | controller | No | Clave primaria decodificada (array/escalar); vacío en alta. |
| `obj_pau` | `mixed` | controller | No | Contexto padre en dossiers. |

Nota: el frontend puede enviar `sel[]`; el controller `tablaDB_formulario_ver.php` lo convierte en
`a_pkey` antes de llamar a este endpoint.

## Salida

- Helper: `ContestarJson::enviar`
- Payload (doble `JSON.parse`):

| Clave | Descripción |
|-------|-------------|
| `fields` | Lista de campos (`tipo`, `nombre`, `etiqueta`, `valor`, `opciones`, `accion`…). |
| `camposForm` | Nombres unidos con `!` para hash del formulario. |
| `camposNo` | Campos excluidos del hash. |
| `tit_txt` | Título. |
| `explicacion_txt` | Explicación. |

## Errores conocidos

- Sin mensajes `_()` en el controller.

## Permisos

- Sin control en el endpoint; edición condicionada en vista por `permiso` del listado.

## Casos De Uso

Lógica inline vía `DatosFormRepo` + `DatosInfoRepoResolver`.

## Frontend Relacionado

- `frontend/shared/controller/tablaDB_formulario_ver.php` (POST interno vía `PostRequest`).
