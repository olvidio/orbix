---
id: "shared.tablaDB_update"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_update.php"
entrada: ["post.clase_info:string", "post.go_to:string", "post.id_pau:string", "post.mod:string", "post.obj_pau:string", "post.s_pkey:string", "post.sel:array"]
entrada_obligatoria: ["clase_info", "mod"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/view/tablaDB_formulario.phtml"]
casos_uso: []
tags: ["shared", "tablaDB", "update"]
estado_revision: "revisado"
---

# TablaDB Update

Persiste altas, ediciones y borrados del formulario genérico `tablaDB` (CRUD vía `Info*` + repositorio).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Motor transversal de mutación para tablas mantenidas con el patrón `DatosInfoRepo` / `DatosFichaInterface`.
Resuelve la clase `Info*` desde `clase_info` (URL-encoded), instancia la ficha según `mod` y delega en
`DatosUpdateRepo`:

- `nuevo` — alta con `getNewId()` (salvo casos especiales `ProfesorLatin` / `ModuloInstalado`).
- `editar` — actualiza campos del POST sobre la ficha cargada con `a_pkey` / `s_pkey`.
- `eliminar` — borra por clave primaria; si viene `sel[]` desde la tabla, extrae `s_pkey` del primer
  token (`pkey#...`).

En éxito devuelve `data: "ok"`; el error va en `mensaje` (string del repositorio o excepción).

## Endpoint

- URL: `/src/shared/tablaDB_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | controller | Sí | Ruta de la clase `Info*` (URL-encoded, p. ej. `src%5Cconfiguracion%5Cdomain%5CInfoApps`). |
| `mod` | `string` | controller | Sí | `nuevo`, `editar` o `eliminar`. Otro valor → mensaje «no se ha ejecutado la acción». |
| `s_pkey` | `string` | controller | No | JSON de clave primaria en base64 url-safe; alternativa a derivar desde `sel[0]`. |
| `sel` | `array` | controller | No | Checkbox de tabla: primer elemento `pkey#...` sustituye `s_pkey`. |
| `id_pau` | `string` | controller | No | Contexto padre (nuevo / FK `id_nom`/`id_ubi`/`id_activ`). |
| `obj_pau` | `string` | controller | No | Objeto padre en dossiers. |
| `go_to` | `string` | controller | No | URL firmada de retorno tras guardar (hidden del formulario). |
| *(campos ficha)* | `mixed` | controller | No | Resto del `$_POST` con nombres de campo de la ficha (`DatosCampo`). |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`, `mensaje: ""`.
- Error: `success: false`, `mensaje` con texto devuelto por `DatosUpdateRepo` o repositorio.

## Errores conocidos

- `no se ha ejecutado la acción` ( `mod` vacío o no reconocido)
- `Ficha o repositorio no configurado` / `Ficha no configurada`
- `Error al ejecutar {metodo} para el campo {nom_camp}: …`
- `Error al guardar: …`
- Texto de `getErrorTxt()` del repositorio en `eliminar`/`editar`
- `No se puede desactivar el módulo: los módulos activos …` (`ModuloInstalado`)

## Permisos

- Sin `perm_*` en el controller; el nivel de edición lo fija el frontend (`permiso`, botón nuevo solo
  con `permiso === 3`) y la autorización de oficina en `$_SESSION['oPerm']` / menú de cada `Info*`.

## Casos De Uso

Lógica inline en el controller vía `DatosUpdateRepo` (sin clase en `application/`).

## Frontend Relacionado

- Submit AJAX de `fnjs_grabar` en `frontend/shared/view/tablaDB_formulario.phtml` →
  `/src/shared/tablaDB_update`.
- Borrado desde listado: `mod=eliminar` vía `tablaDB_lista_ver.phtml` (no llama a este endpoint
  directamente en todos los flujos; el formulario de ficha sí).
