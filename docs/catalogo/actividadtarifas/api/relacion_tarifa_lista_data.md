---
id: "actividadtarifas.relacion_tarifa_lista_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/relacion_tarifa_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_lista_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadtarifas_RelacionTarifaListaDataData"
respuesta_data: ["a_cabeceras:array", "a_valores:array", "puede_anadir:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaListaData"]
tags: ["actividadtarifas", "relacion", "tarifa", "lista", "data"]
estado_revision: "revisado"
---

# Relacion Tarifa Lista Data

Listado de relaciones `TipoTarifa` ↔ tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve todas las relaciones ordenadas por id de tipo de actividad, con columnas nombre del tipo,
tarifa (letra + modo) y enlace modificar si `mi_sfsv` coincide con la sección del tipo y permiso
`adl`. `puede_anadir` para `adl`|`pr`|`calendario`.

## Endpoint

- URL: `/src/actividadtarifas/relacion_tarifa_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/relacion_tarifa_lista_data.php`

## Entrada

Sin parámetros POST.

## Salida

- Helper: `ContestarJson::enviar` → doble `JSON.parse` en cliente.
- Payload: `a_cabeceras` (tipo actividad, tarifa, acción), `a_valores`, `puede_anadir`.

## Permisos

- Enlace modificar: `mi_sfsv === isfsv` del tipo y `have_perm_oficina('adl')`.
- `puede_anadir`: `have_perm_oficina('adl'|'pr'|'calendario')`.

## Casos De Uso

- `src\actividadtarifas\application\RelacionTarifaListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php`.
