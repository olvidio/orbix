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
estado_revision: "revisado"
---

# Tipo Tarifa Lista Data

Listado del catálogo maestro de `TipoTarifa`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve todas las tarifas ordenadas por `sfsv, letra` con columnas id, sección, letra, modo,
observaciones y enlace modificar (solo si `mi_sfsv` coincide y permiso `adl`). Flags
`puede_editar` (`adl`) y `puede_anadir` (`adl`|`pr`|`calendario`).

## Endpoint

- URL: `/src/actividadtarifas/tipo_tarifa_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tipo_tarifa_lista_data.php`

## Entrada

Sin parámetros POST; el caso de uso no lee entrada.

## Salida

- Helper: `ContestarJson::enviar` → doble `JSON.parse` en cliente.
- Payload:
  - `a_cabeceras`, `a_valores` (columna 6 puede ser `{script: fnjs_modificar(id), valor}`)
  - `puede_editar`, `puede_anadir`

## Permisos

- `puede_editar`: `have_perm_oficina('adl')`.
- Enlace modificar por fila: además `mi_sfsv === sfsv` de la tarifa.
- `puede_anadir`: `have_perm_oficina('adl'|'pr'|'calendario')`.

## Casos De Uso

- `src\actividadtarifas\application\TipoTarifaListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_lista.php`: renderiza tabla HTML al cargar `tarifa.phtml`.
