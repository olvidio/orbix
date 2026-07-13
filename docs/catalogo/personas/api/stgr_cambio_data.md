---
id: "personas.stgr_cambio_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/stgr_cambio_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/personas/infrastructure/ui/http/controllers/stgr_cambio_data.php"
entrada: ["post.id_nom:integer", "post.id_tabla:string", "post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No existe la clase de la persona", "No se encuentra la persona"]
frontend_referencias: ["frontend/personas/controller/stgr_cambio.php"]
casos_uso: ["src\\personas\\application\\StgrCambioData"]
tags: ["personas", "stgr", "cambio", "data"]
estado_revision: "revisado"
---

# Stgr Cambio Data

Datos para el formulario modal de cambio de nivel STGR (`stgr_cambio.phtml`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Resuelve persona por `sel` (`id_nom#id_tabla`) o campos sueltos. Usa `repositorioPorIdTabla`
(`n`, `a`, `s`, `sssc`, `x`, `pn`/`pa`…). Devuelve nombre, `nivel_stgr` actual y mapa
`opciones_nivel_stgr` (`NivelStgrId::getArrayNivelStgr`).

## Endpoint

- URL: `/src/personas/stgr_cambio_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/stgr_cambio_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `mixed` | application | No | `id_nom#id_tabla` |
| `id_nom` | `integer` | application | No | |
| `id_tabla` | `string` | application | No | Obligatorio si no viene en `sel` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Claves: `nom`, `nivel_stgr`, `id_nom`, `id_tabla`, `opciones_nivel_stgr`.

## Permisos

- Acceso al formulario controlado en frontend: botón «modificar stgr» requiere `have_perm_oficina('est')`
  en `personas_select.php`.

## Errores conocidos

- `No existe la clase de la persona` (`id_tabla` vacío o no mapeado)
- `No se encuentra la persona`

## Casos De Uso

- `src\personas\application\StgrCambioData`

## Frontend Relacionado

- `frontend/personas/controller/stgr_cambio.php` (desde listado `fnjs_modificar`)
