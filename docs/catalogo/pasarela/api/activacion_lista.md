---
id: "pasarela.activacion_lista"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/activacion_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/activacion_lista.php"
entrada:[]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/activacion_ajax.php"
casos_uso: ["src\pasarela\application\ActivacionLista"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Activacion Lista

Devuelve el listado del parámetro `fecha_activacion`: valor por defecto y excepciones por tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el payload para la tabla de activación. Claves: `default` (texto, p. ej. «3 días» o `upload`) y `excepciones` (array de `{id_tipo_activ, etiqueta, valor}`). El frontend renderiza HTML; este endpoint no genera markup.

## Endpoint

- URL: `/src/pasarela/activacion_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_lista.php`

## Entrada

Sin parámetros POST (listados sin filtros o lectura de configuración persistida).

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el front).
- Éxito: `success: true`, `data` serializado con `default` y `excepciones`.

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Sin control en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\pasarela\application\ActivacionLista`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`