---
id: "pasarela.contribucion_reserva_default_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_reserva_default_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_data.php"
entrada:[]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/contribucion_reserva_ajax.php"
casos_uso: ["src\pasarela\application\ContribucionReservaDefaultData"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Contribucion Reserva Default Data

Valor por defecto de `contribucion_reserva` para `form_default`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Precarga `{default}`.

## Endpoint

- URL: `/src/pasarela/contribucion_reserva_default_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_data.php`

## Entrada

Sin parámetros POST (listados sin filtros o lectura de configuración persistida).

## Salida

- Payload: `{default: string}`.

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ContribucionReservaDefaultData`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`