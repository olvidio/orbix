---
id: "usuarios.grupo_info"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/grupo_info"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/grupo_info.php"
entrada: ["post.id_usuario:integer"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/grupo_form.php"]
casos_uso: []
tags: ["usuarios", "grupo", "info"]
estado_revision: "revisado"
errores: ["Grupo no encontrado"]
---

# Grupo Info

Devuelve el nombre de un grupo para el formulario de edición.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el nombre de un grupo para el formulario de edición.

## Endpoint

- URL: `/src/usuarios/grupo_info`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_info.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `nombre`: nombre del grupo

## Errores conocidos
- `Grupo no encontrado`

## Permisos

Admin en frontend `grupo_form`.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/grupo_form.php"]`).
