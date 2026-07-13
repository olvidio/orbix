---
id: "usuarios.role_grupmenu_info"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_grupmenu_info"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_info.php"
entrada: ["post.id_role:integer"]
entrada_obligatoria: ["id_role"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/role_grupmenu.php"]
casos_uso: []
tags: ["usuarios", "role", "grupmenu", "info"]
estado_revision: "revisado"
errores: ["Rol no encontrado"]
---

# Role Grupmenu Info

Lista grupmenus disponibles para añadir a un rol.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista grupmenus disponibles para añadir a un rol.

## Endpoint

- URL: `/src/usuarios/role_grupmenu_info`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_role` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_cabeceras`: grupmenu
  - `a_botones`: añadir
  - `a_valores`: grupmenus no asignados
  - `role`: nombre rol

## Errores conocidos
- `Rol no encontrado`

## Permisos

Admin en pantalla `role_grupmenu`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/role_grupmenu.php"]`).
