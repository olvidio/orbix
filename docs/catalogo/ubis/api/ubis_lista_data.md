---
id: "ubis.ubis_lista_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_lista_data.php"
entrada: ["post.nombre_ubi:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisListaDataData"
respuesta_data: ["a_cabeceras:list<string>, a_valores: list<array<string|int>>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_lista.php"]
casos_uso: ["src\\ubis\\application\\UbisListaData"]
tags: ["ubis", "lista", "data"]
estado_revision: "revisado"
errores: ["opción no definida en switch en %s, linea %s"]
---

# Ubis Lista Data

Busca casas y centros activos por nombre para el autocompletado de lista.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Busca casas y centros activos por nombre para el autocompletado de lista.

## Endpoint

- URL: `/src/ubis/ubis_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `nombre_ubi` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `a_cabeceras`: cabeceras tabla
  - `a_valores`: filas casas y centros activos

## Errores conocidos
- `opción no definida en switch en %s, linea %s`

## Permisos

have_perm_oficina(vcsd|des): ver sf en sfsv=1 sin filtrar sv.

## Casos De Uso

- `src\ubis\application\UbisListaData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/ubis/controller/ubis_lista.php"]`).
