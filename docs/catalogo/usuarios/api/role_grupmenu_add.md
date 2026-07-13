---
id: "usuarios.role_grupmenu_add"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_grupmenu_add"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_add.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/role_grupmenu.phtml"]
casos_uso: []
tags: ["usuarios", "role", "grupmenu", "add"]
estado_revision: "revisado"
errores: ["hay un error, no se ha guardado", "debe seleccionar uno"]
---

# Role Grupmenu Add

Asocia grupmenu a rol (tokens sel `id_role#id_grupmenu`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Asocia grupmenu a rol (tokens sel `id_role#id_grupmenu`).

## Endpoint

- URL: `/src/usuarios/role_grupmenu_add`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_add.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `hay un error, no se ha guardado`
- `debe seleccionar uno`

## Permisos

Superadmin o admin (permiso 1/2) en `role_grupmenu`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/view/role_grupmenu.phtml"]`).
