---
id: "usuarios.usuario_grupo_del"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_grupo_del"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del.php"
entrada: ["post.ctx:string"]
entrada_obligatoria: ["ctx"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_grupo_del_lst.php", "frontend/usuarios/view/usuario_grupo.phtml"]
casos_uso: []
tags: ["usuarios", "usuario", "grupo", "del"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "hay un error, no se ha eliminado"]
---

# Usuario Grupo Del

Quita grupo permisos del usuario (ctx HashB `usuario_grupo_del`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Quita grupo permisos del usuario (ctx HashB `usuario_grupo_del`).

## Endpoint

- URL: `/src/usuarios/usuario_grupo_del`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del.php`

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
- `hay un error, no se ha eliminado`

## Permisos

Admin; ctx firmado.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_grupo_del_lst.php", "frontend/usuarios/view/usuario_grupo.phtml"]`).
