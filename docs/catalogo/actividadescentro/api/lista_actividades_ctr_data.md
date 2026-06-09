---
id: "actividadescentro.lista_actividades_ctr_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/lista_actividades_ctr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/lista_actividades_ctr_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.periodo:string", "post.tipo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadescentro_ListaActividadesCtrDataData"
respuesta_data: ["titulo:string", "tipo:string", "inicio_iso:string", "fin_iso:string", "filas:list<array<string, mixed>>"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividadescentro\\application\\ListaActividadesCtrData"]
tags: ["actividadescentro", "lista", "actividades", "ctr", "data"]
estado_revision: "generado"
---

# Lista Actividades Ctr Data

Endpoint backend: devuelve el listado de actividades del tipo + periodo elegidos, junto con los centros encargados de cada una y los flags de permiso (ver / modificar / crear) para cada fila.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadescentro/lista_actividades_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/lista_actividades_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller+application | No | controller+application |
| `empiezamin` | `string` | controller+application | No | controller+application |
| `periodo` | `string` | controller+application | No | controller+application |
| `tipo` | `string` | controller+application | No | controller+application |
| `year` | `string` | controller+application | No | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadescentro_ListaActividadesCtrDataData`):
  - `titulo` (`string`)
  - `tipo` (`string`)
  - `inicio_iso` (`string`)
  - `fin_iso` (`string`)
  - `filas` (`list<array<string, mixed>>`)

## Casos De Uso

- `src\actividadescentro\application\ListaActividadesCtrData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.