---
id: "pasarela.activacion_lista"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/activacion_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/activacion_lista.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "pasarela_ActivacionListaData"
respuesta_data: ["default:string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/pasarela/controller/activacion_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ActivacionLista"]
tags: ["pasarela", "activacion", "lista"]
estado_revision: "generado"
---

# Activacion Lista

Devuelve el listado del parámetro `fecha_activacion` listo para serializar: - `default`: valor por defecto. - `excepciones`: array de filas `{id_tipo_activ, etiqueta, valor}`. El frontend renderiza la tabla a partir de estos datos; este caso de uso no genera HTML.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/activacion_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_lista.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `pasarela_ActivacionListaData`):
  - `default` (`string, excepciones: list<array{id_tipo_activ: string, etiqueta: string, valor: string}>`)

## Casos De Uso

- `src\pasarela\application\ActivacionLista`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.