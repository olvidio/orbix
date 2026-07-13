---
id: "usuarios.usuario_grupo_del_lst"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_grupo_del_lst"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del_lst.php"
entrada: ["post.id_usuario:integer"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/usuario_grupo_del_lst.php"]
casos_uso: []
tags: ["usuarios", "usuario", "grupo", "del", "lst"]
estado_revision: "revisado"
errores: []
---

# Usuario Grupo Del Lst

Lista grupos ya asignados al usuario con acción quitar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista grupos ya asignados al usuario con acción quitar.

## Endpoint

- URL: `/src/usuarios/usuario_grupo_del_lst`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_grupo_del_lst.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_cabeceras`: grupos asignados
  - `a_valores`: acción quitar con ctx HashB

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Admin en ficha usuario.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/usuario_grupo_del_lst.php"]`).
