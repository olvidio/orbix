---
id: "menus.menus_get_page_data"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_get_page_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_get_page_data.php"
entrada: ["post.filtro_grupo:string", "post.id_menu:string", "post.nuevo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/menus/controller/menus_get.php"]
casos_uso: ["src\\menus\\application\\MenusGetPageData"]
tags: ["menus", "get", "page", "data"]
estado_revision: "generado"
---

# Menus Get Page Data

Datos para `frontend/menus/controller/menus_get.php` (formulario o listado).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menus_get_page_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_get_page_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_grupo` | `string` | application | No | application |
| `id_menu` | `string` | application | No | application |
| `nuevo` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\menus\application\MenusGetPageData`

## Frontend Relacionado

- `frontend/menus/controller/menus_get.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.