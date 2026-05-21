---
id: "pasarela.contribucion_reserva_lista"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_reserva_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/pasarela/controller/contribucion_reserva_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionReservaLista"]
tags: ["pasarela", "contribucion", "reserva", "lista"]
estado_revision: "generado"
---

# Contribucion Reserva Lista

Devuelve el listado del parámetro `contribucion_reserva` listo para serializar. Estructura: `{default, excepciones: [{id_tipo_activ, etiqueta, valor}]}`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/contribucion_reserva_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_lista.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\pasarela\application\ContribucionReservaLista`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.