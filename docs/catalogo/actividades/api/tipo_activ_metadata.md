---
id: "actividades.tipo_activ_metadata"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_metadata"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_metadata.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_TipoActivMetadataData"
respuesta_data: ["maps:object", "filas:array"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/helpers/ActividadTipo.php", "frontend/actividades/helpers/TipoActivMetadataLoader.php", "frontend/actividades/helpers/TiposDeActividades.php"]
casos_uso: ["src\\actividades\\application\\TipoActivMetadata"]
tags: ["actividades", "tipo", "activ", "metadata"]
estado_revision: "revisado"
---

# Tipo Activ Metadata

Devuelve, en una sola respuesta JSON, los datos de referencia que necesita el espejo en frontend
(`frontend\actividades\helpers\TiposDeActividades`) para resolver los tipos de actividad sin tocar el
repositorio. Sustituye al antiguo `tipo_activ_filas`, que solo devolvía las filas y obligaba al
frontend a duplicar los mapas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Provee dos bloques de metadatos para construir/decodificar el `id_tipo_activ` en el frontend:

- `maps`: los 4 mapas estáticos texto→código (constantes públicas de `TiposActividades`): `sfsv`,
  `asistentes`, `actividad1digito`, `actividad2digitos`.
- `filas`: la lista plana `{id_tipo_activ, nombre}` de todos los tipos existentes (para resolver los
  "posibles" en memoria).

Está pensado para una única request por carga de página; el loader frontend cachea el payload completo.

## Endpoint

- URL: `/src/actividades/tipo_activ_metadata`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_metadata.php`

## Entrada

Sin parámetros. El caso de uso no lee `$_POST`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` (schema `actividades_TipoActivMetadataData`) tiene dos claves:
  - `maps`: objeto con 4 sub-mapas `sfsv`, `asistentes`, `actividad1digito`, `actividad2digitos`
    (cada uno `texto → código`).
  - `filas`: lista de objetos `{id_tipo_activ:int, nombre:string}` ordenada por `id_tipo_activ`.

## Permisos

- Sin control de permisos propio; se trata de metadatos de catálogo. La autorización de contexto la
  resuelve el frontend y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividades\application\TipoActivMetadata`

## Frontend Relacionado

- `frontend/actividades/helpers/TipoActivMetadataLoader.php` (consume y cachea el payload).
- `frontend/actividades/helpers/TiposDeActividades.php` (espejo que usa los mapas).
- `frontend/actividades/helpers/ActividadTipo.php`
