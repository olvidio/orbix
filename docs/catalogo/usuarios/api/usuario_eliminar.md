---
id: "usuarios.usuario_eliminar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_eliminar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_usuarioEliminarData"
respuesta_data: ["error:string, data: string"]
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_lista.php"]
casos_uso: ["src\\usuarios\\application\\usuarioEliminar"]
tags: ["usuarios", "usuario", "eliminar"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "hay un error, no se ha eliminado"]
---

# Usuario Eliminar

Elimina usuario seleccionado (id en token sel).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina usuario seleccionado (id en token sel).

## Endpoint

- URL: `/src/usuarios/usuario_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Usuario no encontrado`
- `hay un error, no se ha eliminado`

## Permisos

Admin id_role≤3 en `usuario_lista`.

## Casos De Uso

- `src\usuarios\application\usuarioEliminar`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_lista.php"]`).
