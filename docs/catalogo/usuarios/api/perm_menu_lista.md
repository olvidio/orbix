---
id: "usuarios.perm_menu_lista"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/perm_menu_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/perm_menu_lista.php"
entrada: ["post.id_usuario:string"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/grupo_form.php"]
casos_uso: []
tags: ["usuarios", "perm", "menu", "lista"]
estado_revision: "revisado"
errores: []
---

# Perm Menu Lista

Lista permisos de menú DL asignados al usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista permisos de menú DL asignados al usuario.

## Endpoint

- URL: `/src/usuarios/perm_menu_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/perm_menu_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `string` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_cabeceras`: oficina o grupo
  - `a_botones`: quitar
  - `a_valores`: filas sel id_usuario#id_item

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Admin en ficha usuario.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/grupo_form.php"]`).
