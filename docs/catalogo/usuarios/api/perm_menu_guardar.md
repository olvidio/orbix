---
id: "usuarios.perm_menu_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/perm_menu_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/perm_menu_guardar.php"
entrada: ["post.id_item:integer", "post.id_usuario:integer", "post.menu_perm:array"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/perm_menu_form.phtml"]
casos_uso: []
tags: ["usuarios", "perm", "menu", "guardar"]
estado_revision: "revisado"
errores: ["Permiso de menú no encontrado", "hay un error, no se ha guardado"]
---

# Perm Menu Guardar

Alta/edición permiso menú: suma bits `menu_perm[]` en entero.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta/edición permiso menú: suma bits `menu_perm[]` en entero.

## Endpoint

- URL: `/src/usuarios/perm_menu_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | application | No | |
| `id_usuario` | `integer` | application | Si | |
| `menu_perm` | `array` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Permiso de menú no encontrado`
- `hay un error, no se ha guardado`

## Permisos

Admin en modal `perm_menu_form`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/view/perm_menu_form.phtml"]`).
