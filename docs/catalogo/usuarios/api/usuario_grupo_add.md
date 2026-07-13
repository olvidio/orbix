---
id: "usuarios.usuario_grupo_add"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_grupo_add"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_add.php"
entrada: ["post.ctx:string"]
entrada_obligatoria: ["ctx"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/usuario_grupo.phtml"]
casos_uso: []
tags: ["usuarios", "usuario", "grupo", "add"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "hay un error, no se ha guardado"]
---

# Usuario Grupo Add

Asocia grupo permisos a usuario (ctx HashB `usuario_grupo_add`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Asocia grupo permisos a usuario (ctx HashB `usuario_grupo_add`).

## Endpoint

- URL: `/src/usuarios/usuario_grupo_add`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_add.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacío serializado).

## Errores conocidos
- `Operación no autorizada`
- `hay un error, no se ha guardado`

## Permisos

Admin; ctx firmado con id_usuario+id_grupo.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/view/usuario_grupo.phtml"]`).
