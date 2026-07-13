---
id: "pasarela.nombre_lista"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/nombre_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/nombre_lista.php"
entrada:[]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/nombre_ajax.php"
casos_uso: ["src\pasarela\application\NombreLista"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Nombre Lista

Listado del parámetro `nombre` (nombres particulares por tipo de actividad).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Solo `excepciones` (sin default). Array `{id_tipo_activ, etiqueta, valor}`.

## Endpoint

- URL: `/src/pasarela/nombre_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/nombre_lista.php`

## Entrada

Sin parámetros POST (listados sin filtros o lectura de configuración persistida).

## Salida

- Payload: `{excepciones: [...]}`.

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\NombreLista`

## Frontend Relacionado

- `frontend/pasarela/controller/nombre_ajax.php`