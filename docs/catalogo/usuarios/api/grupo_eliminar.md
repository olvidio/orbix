---
id: "usuarios.grupo_eliminar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/grupo_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/grupo_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/grupo_lista.phtml"]
casos_uso: []
tags: ["usuarios", "grupo", "eliminar"]
estado_revision: "revisado"
errores: ["Grupo no encontrado", "hay un error, no se ha eliminado"]
---

# Grupo Eliminar

Elimina un grupo de permisos (`aux_grupos`) seleccionado en la lista.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina un grupo de permisos (`aux_grupos`) seleccionado en la lista.

## Endpoint

- URL: `/src/usuarios/grupo_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Grupo no encontrado`
- `hay un error, no se ha eliminado`

## Permisos

Admin id_role≤3 en frontend `grupo_lista`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/view/grupo_lista.phtml"]`).
