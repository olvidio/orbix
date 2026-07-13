---
id: "actividadessacd.locales_desplegable_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/locales_desplegable_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/locales_desplegable_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_LocalesDesplegableDataData"
respuesta_data: ["a_locales:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_txt.php"]
casos_uso: ["src\\actividadessacd\\application\\LocalesDesplegableData"]
tags: ["actividadessacd", "locales", "desplegable", "data"]
estado_revision: "revisado"
---

# Locales Desplegable Data

Devuelve el mapa de locales (idiomas) instalados para poblar el desplegable `idioma` del editor de
textos de comunicación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Sin parámetros: devuelve `{a_locales: LocalRepository::getArrayLocales()}` (mapa `locale => etiqueta`).

## Endpoint

- URL: `/src/actividadessacd/locales_desplegable_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/locales_desplegable_data.php`

## Entrada

Sin parámetros. El controller invoca directamente `execute()` sin leer `$_POST`.

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute())` — `data` es el payload serializado como
  string JSON; el front hace un segundo `JSON.parse`.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_LocalesDesplegableDataData`):
  - `a_locales` (`array`): mapa de locales disponibles (`locale => etiqueta`).

## Permisos

- El caso de uso no aplica control de permisos propio. La autorización se resuelve en el frontend
  (`com_sacd_txt.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadessacd\application\LocalesDesplegableData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_txt.php` (consume el payload vía
  `ActividadessacdPayload::localesFromPayload` para montar el desplegable `idioma`).
