---
id: "usuarios.role_info"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_info"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_info.php"
entrada: ["post.id_role:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/role_form.php"]
casos_uso: []
tags: ["usuarios", "role", "info"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "Rol no encontrado"]
---

# Role Info

Carga ficha rol: datos, permiso de edición y tabla grupmenus ya asignados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Carga ficha rol: datos, permiso de edición y tabla grupmenus ya asignados.

## Endpoint

- URL: `/src/usuarios/role_info`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_role` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `role`: nombre
  - `chk_sf`: checked|''
  - `chk_sv`: checked|''
  - `pau`: string
  - `chk_dmz`: checked|''
  - `permiso`: 0|1|2
  - `a_cabeceras`: tabla grupmenus asignados
  - `a_valores`: filas

## Errores conocidos
- `Usuario no encontrado`
- `Rol no encontrado`

## Permisos

permiso=1 superadmin (CRUD rol); permiso=2 admin (solo grupmenu).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/role_form.php"]`).
