---
id: "pasarela.contribucion_no_duerme_default_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_no_duerme_default_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_default_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/pasarela/controller/contribucion_no_duerme_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionNoDuermeDefaultData"]
tags: ["pasarela", "contribucion", "no", "duerme", "default", "data"]
estado_revision: "generado"
---

# Contribucion No Duerme Default Data

Devuelve solo el valor por defecto del parámetro `contribucion_no_duerme`, para alimentar el formulario `form_default` desde el frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/contribucion_no_duerme_default_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_no_duerme_default_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\pasarela\application\ContribucionNoDuermeDefaultData`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.