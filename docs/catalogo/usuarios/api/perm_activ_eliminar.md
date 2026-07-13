---
id: "usuarios.perm_activ_eliminar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/perm_activ_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/perm_activ_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "perm", "activ", "eliminar"]
estado_revision: "revisado"
errores: ["no existe el registro", "hay un error, no se ha eliminado"]
---

# Perm Activ Eliminar

Elimina un permiso de actividad-proceso del usuario (`id_item` en token sel).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina un permiso de actividad-proceso del usuario (`id_item` en token sel).

## Endpoint

- URL: `/src/usuarios/perm_activ_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_activ_eliminar.php`

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

Admin en ficha usuario (`perm_activ_lista`).

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
