---
id: "menus.menu_guardar"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menu_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menu_guardar.php"
entrada: ["post.filtro_grupo:integer", "post.id_menu:integer", "post.id_metamenu:integer", "post.ok:string", "post.orden:string", "post.parametros:string", "post.perm_menu:array", "post.txt_menu:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\menus\\application\\MenuGuardar"]
tags: ["menus", "menu", "guardar"]
estado_revision: "generado"
---

# Menu Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menu_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menu_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_grupo` | `integer` | controller | No | controller |
| `id_menu` | `integer` | controller | No | controller |
| `id_metamenu` | `integer` | controller | No | controller |
| `ok` | `string` | controller | No | controller |
| `orden` | `string` | controller | No | controller |
| `parametros` | `string` | controller | No | controller |
| `perm_menu` | `array` | controller | No | controller |
| `txt_menu` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\menus\application\MenuGuardar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.