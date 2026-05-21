---
id: "notas.acta_ver_form_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_ver_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/notas/infrastructure/ui/http/controllers/acta_ver_form_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaVerFormData"]
tags: ["notas", "acta", "ver", "form", "data"]
estado_revision: "generado"
---

# Acta Ver Form Data

Estado del formulario `acta_ver` (sin HashFront ni vistas).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_ver_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_ver_form_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`

## Casos De Uso

- `src\notas\application\ActaVerFormData`

## Frontend Relacionado

- `frontend/notas/controller/acta_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.