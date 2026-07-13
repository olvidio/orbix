---
id: "pasarela.activacion_default_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/activacion_default_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/activacion_default_data.php"
entrada:[]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/activacion_ajax.php"
casos_uso: ["src\pasarela\application\ActivacionDefaultData"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Activacion Default Data

Devuelve el valor por defecto del parámetro `fecha_activacion` para el formulario `form_default`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Precarga el campo `default` del formulario de valor por defecto (días antes del inicio o `upload`).

## Endpoint

- URL: `/src/pasarela/activacion_default_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_default_data.php`

## Entrada

Sin parámetros POST (listados sin filtros o lectura de configuración persistida).

## Salida

- Payload: `{default: string}`.

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ActivacionDefaultData`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`