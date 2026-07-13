---
id: "usuarios.role_eliminar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/role_lista.phtml"]
casos_uso: []
tags: ["usuarios", "role", "eliminar"]
estado_revision: "revisado"
errores: ["no existe el registro", "hay un error, no se ha eliminado"]
---

# Role Eliminar

Elimina un rol (solo superadmin permiso=1).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina un rol (solo superadmin permiso=1).

## Endpoint

- URL: `/src/usuarios/role_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `no existe el registro`
- `hay un error, no se ha eliminado`

## Permisos

Superadmin id_role=1 en `rolesLista`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/view/role_lista.phtml"]`).
