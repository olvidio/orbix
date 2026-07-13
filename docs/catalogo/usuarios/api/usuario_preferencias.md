---
id: "usuarios.usuario_preferencias"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/usuario_preferencias"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/usuario_preferencias.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/preferencias.php"]
casos_uso: []
tags: ["usuarios", "usuario", "preferencias"]
estado_revision: "revisado"
errores: []
---

# Usuario Preferencias

Builder pantalla preferencias: layout, inicio, oficina, estilo y opciones según rol/apps.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Builder pantalla preferencias: layout, inicio, oficina, estilo y opciones según rol/apps.

## Endpoint

- URL: `/src/usuarios/usuario_preferencias`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/usuario_preferencias.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `layout`: legacy|pills
  - `inicio`: string
  - `oficina`: id_grupmenu
  - `oficinas_posibles`: map
  - `estilo_color`: string
  - `tipo_menu`: string
  - `aOpciones`: páginas inicio

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Cualquier usuario autenticado.

## Casos De Uso

- _(lógica inline en controller)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/preferencias.php"]`).
