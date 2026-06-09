---
id: "menus.menu_mover"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menu_mover"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menu_mover.php"
entrada: ["post.gm_new:string", "post.id_menu:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\menus\\application\\MenuMover"]
tags: ["menus", "menu", "mover"]
estado_revision: "generado"
---

# Menu Mover

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menu_mover`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menu_mover.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `gm_new` | `string` | controller | No | controller |
| `id_menu` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\menus\application\MenuMover`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.