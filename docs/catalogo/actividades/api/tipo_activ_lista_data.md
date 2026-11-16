---
id: "actividades.tipo_activ_lista_data"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_lista_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/tipo_activ_lista.php"]
casos_uso: ["src\\actividades\\application\\TipoActivListaData"]
tags: ["actividades", "tipo", "activ", "lista", "data"]
estado_revision: "revisado"
---

# Tipo Activ Lista Data

Devuelve los tipos de actividad ordenados por `id_tipo_activ`, para la tabla del gestor de tipos (`tipo_activ.php`).

## Endpoint

- URL: `/src/actividades/tipo_activ_lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_lista_data.php`

## Entrada

Sin parámetros.

## Salida

- `ContestarJson::enviar` con `data`: `{ "tipos": [ { "id_tipo_activ": int, "nom": string }, ... ] }`.
- La tabla (`frontend\shared\web\Lista`, con el enlace «modificar» a `fnjs_modificar`) la monta
  `frontend/actividades/controller/tipo_activ_lista.php`, que además vacía la caché de
  `TipoActivMetadataLoader`.

## Casos De Uso

- `src\actividades\application\TipoActivListaData`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).
