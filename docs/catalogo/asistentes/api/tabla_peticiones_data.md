---
id: "asistentes.tabla_peticiones_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/tabla_peticiones_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/asistentes/infrastructure/ui/http/controllers/tabla_peticiones_data.php"
entrada: ["post.id_activ_old:integer", "post.restored_id_sel:mixed", "post.restored_scroll_id:mixed", "post.sel:array", "post.stack:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/tabla_peticiones.php"]
casos_uso: ["src\\asistentes\\application\\TablaPeticionesData"]
tags: ["asistentes", "tabla", "peticiones", "data"]
estado_revision: "revisado"
---

# Tabla Peticiones Data

Tabla de peticiones de plaza por asistente de una actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para cada asistente activo de la actividad, lista sus peticiones de plaza (`PlazaPeticion`) vigentes
con plazas libres/concedidas. Las peticiones distintas de la actividad actual son enlaces de movimiento
rápido (`mod=mover` → `asistente_guardar`). Restaura selección/scroll si viene de `stack`.

## Endpoint

- URL: `/src/asistentes/tabla_peticiones_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/tabla_peticiones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | application | No | Token `id_activ#nom_activ` |
| `id_activ_old` | `integer` | application | No | Alternativa a `sel` |
| `stack` | `mixed` | application | No | NavStack: habilita restore |
| `restored_id_sel`, `restored_scroll_id` | `mixed` | application | No | Estado UI restaurado |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `nom_activ`, `a_cabeceras`, `a_botones`
  - `a_valores`: filas con `peticiones_parts` (texto o enlace mover con hidden)
  - `paths.asistente_guardar`

## Permisos

- Sin control propio; requiere `actividadplazas`; acceso desde actividad: frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\asistentes\application\TablaPeticionesData`

## Frontend Relacionado

- `frontend/asistentes/controller/tabla_peticiones.php` + `TablaPeticionesRender` (desde `actividades.js`).
