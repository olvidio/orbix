---
id: "usuarios.role_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_guardar.php"
entrada: ["post.role:string", "post.id_role:integer", "post.sf:integer", "post.sv:integer", "post.pau:string", "post.dmz:integer"]
entrada_obligatoria: ["role"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/role_form.phtml"]
casos_uso: []
tags: ["usuarios", "role", "guardar"]
estado_revision: "revisado"
errores: ["Rol no encontrado", "hay un error, no se ha guardado", "debe poner un nombre"]
---

# Role Guardar

Crea o actualiza rol con flags sf/sv, pau y dmz.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea o actualiza rol con flags sf/sv, pau y dmz.

## Endpoint

- URL: `/src/usuarios/role_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `role` | `string` | application | Si | |
| `id_role` | `integer` | application | No | |
| `sf` | `integer` | application | No | |
| `sv` | `integer` | application | No | |
| `pau` | `string` | application | No | |
| `dmz` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Rol no encontrado`
- `hay un error, no se ha guardado`
- `debe poner un nombre`

## Permisos

Superadmin en `role_form`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/view/role_form.phtml"]`).
