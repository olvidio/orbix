---
id: "actividades.tipo_activ_metadata"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_metadata"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_metadata.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_TipoActivMetadataData"
respuesta_data: ["maps:array", "sfsv:array", "asistentes:array", "actividad1digito:array", "actividad2digitos:array", "filas:list<array{id_tipo_activ:int, nombre:string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/helpers/ActividadTipo.php", "frontend/actividades/helpers/TipoActivMetadataLoader.php", "frontend/actividades/helpers/TiposDeActividades.php"]
casos_uso: ["src\\actividades\\application\\TipoActivMetadata"]
tags: ["actividades", "tipo", "activ", "metadata"]
estado_revision: "generado"
---

# Tipo Activ Metadata

Endpoint backend que devuelve, en una sola respuesta JSON, los datos que necesita {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/tipo_activ_metadata`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_metadata.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividades_TipoActivMetadataData`):
  - `maps` (`array`)
  - `sfsv` (`array`)
  - `asistentes` (`array`)
  - `actividad1digito` (`array`)
  - `actividad2digitos` (`array`)
  - `filas` (`list<array{id_tipo_activ:int, nombre:string}>`)

## Efectos colaterales

- Devuelve en una sola respuesta TODO lo que necesita el espejo en frontend {@see \frontend\actividades\helpers\TiposDeActividades} para funcionar sin tocar el repositorio: - `maps`: los 4 mapas estáticos texto→código del id_tipo_activ (sfsv, asistentes, actividad 1 dígito, actividad 2 dígitos).

## Casos De Uso

- `src\actividades\application\TipoActivMetadata`

## Frontend Relacionado

- `frontend/actividades/helpers/ActividadTipo.php`
- `frontend/actividades/helpers/TipoActivMetadataLoader.php`
- `frontend/actividades/helpers/TiposDeActividades.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.