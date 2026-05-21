---
id: "pasarela.nombre_lista"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/nombre_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/nombre_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/pasarela/controller/nombre_ajax.php"]
casos_uso: ["src\\pasarela\\application\\NombreLista"]
tags: ["pasarela", "nombre", "lista"]
estado_revision: "generado"
---

# Nombre Lista

Devuelve el listado del parámetro `nombre` listo para serializar. Estructura: `{excepciones: [{id_tipo_activ, etiqueta, valor}]}`. (El parámetro `nombre` no tiene valor por defecto.)

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/nombre_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/nombre_lista.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\pasarela\application\NombreLista`

## Frontend Relacionado

- `frontend/pasarela/controller/nombre_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.