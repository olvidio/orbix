---
id: "actividadtarifas.tipo_tarifa_lista_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tipo_tarifa_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_lista_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadtarifas_TipoTarifaListaDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "puede_editar:boolean", "puede_anadir:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa.php", "frontend/actividadtarifas/controller/tarifa_lista.php"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaListaData"]
tags: ["actividadtarifas", "tipo", "tarifa", "lista", "data"]
estado_revision: "generado"
---

# Tipo Tarifa Lista Data

Endpoint backend: listado del catalogo de tipos de tarifa.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_lista_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `actividadtarifas_TipoTarifaListaDataData`):
  - `a_cabeceras` (`array`)
  - `a_valores` (`array`)
  - `puede_editar` (`boolean`)
  - `puede_anadir` (`boolean`)

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa.php`
- `frontend/actividadtarifas/controller/tarifa_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.