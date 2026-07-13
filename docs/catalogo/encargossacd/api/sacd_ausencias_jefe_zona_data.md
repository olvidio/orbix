---
id: "encargossacd.sacd_ausencias_jefe_zona_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ausencias_jefe_zona_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_jefe_zona_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdAusenciasJefeZonaDataData"
respuesta_data: ["a_sacd:array"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasJefeZonaData"]
tags: ["encargossacd", "sacd", "ausencias", "jefe", "zona", "data"]
estado_revision: "revisado"
---
# Sacd Ausencias Jefe Zona Data

Datos para el listado de SACDs susceptibles de gestionar ausencias desde la ficha de jefe de zona (`frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`). Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde (Oficial_dl o jefe de calendario), la totalidad de SACDs activos. El array se devuelve ordenado por iniciales para alimentar el desplegable.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista de SACDs con ausencias para jefe de zona (vista exterior).

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_jefe_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_jefe_zona_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Claves: `a_sacd[]` (doble `JSON.parse`).


## Permisos

Vista exterior sacd; sin permisos propios en builder.

## Casos De Uso

- `src\encargossacd\application\SacdAusenciasJefeZonaData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`

