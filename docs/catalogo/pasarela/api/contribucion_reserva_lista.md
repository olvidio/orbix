---
id: "pasarela.contribucion_reserva_lista"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_reserva_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_lista.php"
entrada:[]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/contribucion_reserva_ajax.php"
casos_uso: ["src\pasarela\application\ContribucionReservaLista"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Contribucion Reserva Lista

Listado del parámetro `contribucion_reserva` (porcentaje reserva).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Payload `{default, excepciones}`.

## Endpoint

- URL: `/src/pasarela/contribucion_reserva_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_lista.php`

## Entrada

Sin parámetros POST (listados sin filtros o lectura de configuración persistida).

## Salida

- `default` y `excepciones` con porcentajes.

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ContribucionReservaLista`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`