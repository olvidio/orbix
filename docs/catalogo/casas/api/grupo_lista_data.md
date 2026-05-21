---
id: "casas.grupo_lista_data"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_lista_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "casas_GrupoCasaListaDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "puede_anadir:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/grupo_lista.php"]
casos_uso: ["src\\casas\\application\\GrupoCasaListaData"]
tags: ["casas", "grupo", "lista", "data"]
estado_revision: "generado"
---

# Grupo Lista Data

Endpoint backend: listado de `GrupoCasa` (relaciones padre ↔ hijo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/casas/grupo_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_lista_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `casas_GrupoCasaListaDataData`):
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)
  - `puede_anadir` (`boolean`)

## Permisos

- Permiso oficina `adl`

## Casos De Uso

- `src\casas\application\GrupoCasaListaData`

## Frontend Relacionado

- `frontend/casas/controller/grupo_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.