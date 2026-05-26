---
id: "ubis.casas_opciones_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/casas_opciones_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/ubis/infrastructure/ui/http/controllers/casas_opciones_data.php"
entrada: ["post.active:string", "post.sv:string", "post.sf:string", "post.id_ubi_in:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["opciones:object"]
requiere_hashb: false
frontend_referencias: ["frontend/shared/web/CasasQue.php", "frontend/planning/controller/planning_casa_que.php"]
casos_uso: ["src\\ubis\\application\\CasasOpcionesData"]
tags: ["ubis", "casas", "opciones", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Casas Opciones Data

Listado `id_ubi → nombre` para el desplegable de casas (`CasasQue`, planning por casas cuando `cdc_sel=9`).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md)

## Endpoint

- URL: `/src/ubis/casas_opciones_data`
- Métodos: `POST` (recomendado)
- Controller: `src/ubis/infrastructure/ui/http/controllers/casas_opciones_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `active` | string | No | `1` (default) solo activas |
| `sv` | string | No | `1` filtra sv |
| `sf` | string | No | `1` filtra sf |
| `id_ubi_in` | string | No | IDs separados por coma (rol CDC) |

El filtro proviene de [`planning_casa_que_data`](../../planning/api/planning_casa_que_data.md) → campo `filtro`.

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `opciones` | object | Mapa `id_ubi → nombre` |

## Cliente de referencia

- `orbix-android`: `fetchCasasOpciones()` — cuando el usuario elige «Una casa o lugar».
