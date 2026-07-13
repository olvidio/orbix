---
id: "configuracion.modulos_update"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/modulos_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/configuracion/infrastructure/ui/http/controllers/modulos_update.php"
entrada: ["post.descripcion:string", "post.id_mod:integer", "post.mod:string", "post.nom:string", "post.sel:mixed", "post.sel_apps:array", "post.sel_mods:array"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
errores: ["hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/configuracion/controller/modulos_update.php"]
casos_uso: ["src\\configuracion\\application\\ModulosUpdateAction"]
tags: ["configuracion", "modulos", "update"]
estado_revision: "revisado"
---

# Modulos Update

Alta / baja / modificación de un `Modulo`. Responde texto plano para el AJAX legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

El caso de uso `ModulosUpdateAction` despacha según el campo `mod`:

- **`nuevo`**: si `nom` no está vacío, crea un `Modulo` (nombre, descripción, módulos y
  apps requeridos) con un id nuevo del repositorio.
- **`eliminar`**: resuelve `id_mod` (del token `sel[0]` antes de `#`, o del campo
  `id_mod`), busca el módulo y lo elimina.
- **defecto (modificar)**: carga el módulo por `id_mod` y actualiza nombre, descripción,
  `sel_mods` y `sel_apps`.

## Endpoint

- URL: `/src/configuracion/modulos_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/modulos_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `mod` | `string` | application | No | `nuevo` / `eliminar` / (vacío = modificar) |
| `id_mod` | `integer` | application | No | Id del módulo (en `eliminar`/modificar); si llega `sel`, se toma su token |
| `nom` | `string` | application | No | Nombre del módulo (obligatorio de facto para `nuevo`) |
| `descripcion` | `string` | application | No | Descripción del módulo |
| `sel` | `mixed` | application | No | Array del listado; `sel[0]` (token antes de `#`) sobreescribe `id_mod` |
| `sel_mods` | `array` | application | No | Ids de módulos requeridos (lista de enteros) |
| `sel_apps` | `array` | application | No | Ids de aplicaciones requeridas (lista de enteros) |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo` (el controller emite `Content-Type: text/plain`).
- Forma: `raw_response`.
- Éxito: cadena vacía (`""`).
- Error: mensaje de texto plano. En `eliminar` fallido devuelve `hay un error, no se ha eliminado` seguido del detalle del repositorio (`getErrorTxt()`).

## Errores conocidos

- `hay un error, no se ha eliminado`

## Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se
  resuelve en el frontend (`modulos_update.php` / form de módulos) y en
  `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\configuracion\application\ModulosUpdateAction`

## Frontend Relacionado

- `frontend/configuracion/controller/modulos_update.php` (envía el POST y trata la respuesta de texto plano con `AjaxJsonSupport::fromPlainText`).
