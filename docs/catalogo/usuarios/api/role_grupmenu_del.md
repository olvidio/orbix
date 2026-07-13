---
id: "usuarios.role_grupmenu_del"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_grupmenu_del"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_del.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/role_form.phtml"]
casos_uso: []
tags: ["usuarios", "role", "grupmenu", "del"]
estado_revision: "revisado"
errores: ["no existe el registro", "hay un error, no se ha eliminado", "debe seleccionar uno"]
---

# Role Grupmenu Del

Quita asociación grupmenu↔rol por id_item.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Quita asociación grupmenu↔rol por id_item.

## Endpoint

- URL: `/src/usuarios/role_grupmenu_del`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_grupmenu_del.php`

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
- `debe seleccionar uno`

## Permisos

Superadmin o admin en ficha rol.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/view/role_form.phtml"]`).
