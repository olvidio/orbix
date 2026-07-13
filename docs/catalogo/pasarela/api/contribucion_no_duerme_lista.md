---
id: "pasarela.contribucion_no_duerme_lista"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_no_duerme_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_lista.php"
entrada:[]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/contribucion_no_duerme_ajax.php"
casos_uso: ["src\pasarela\application\ContribucionNoDuermeLista"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Contribucion No Duerme Lista

Listado del parámetro `contribucion_no_duerme` (porcentaje 0–100).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Payload `{default, excepciones}` con la misma forma que activación.

## Endpoint

- URL: `/src/pasarela/contribucion_no_duerme_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_lista.php`

## Entrada

Sin parámetros POST (listados sin filtros o lectura de configuración persistida).

## Salida

- `default` y `excepciones` (porcentajes enteros como string).

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ContribucionNoDuermeLista`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`