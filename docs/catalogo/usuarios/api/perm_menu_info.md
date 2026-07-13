---
id: "usuarios.perm_menu_info"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/perm_menu_info"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/perm_menu_info.php"
entrada: ["post.id_usuario:integer", "post.id_item:integer"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/perm_menu_form.php"]
casos_uso: []
tags: ["usuarios", "perm", "menu", "info"]
estado_revision: "revisado"
errores: ["Grupo no encontrado"]
---

# Perm Menu Info

Carga formulario modal de permiso menú (nuevo o edición).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga formulario modal de permiso menú (nuevo o edición).

## Endpoint

- URL: `/src/usuarios/perm_menu_info`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |
| `id_item` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `nombre`: nombre grupo/usuario
  - `menu_perm`: entero bits
  - `menu_perm_dl_map`: mapa bits etiquetados

## Errores conocidos
- `Grupo no encontrado`

## Permisos

Admin en `perm_menu_form`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/perm_menu_form.php"]`).
