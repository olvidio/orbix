---
id: "actividades.tipo_activ_form_modificar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/tipo_activ_form_modificar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_modificar.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivFormModificar"]
tags: ["actividades", "tipo", "activ", "form", "modificar"]
estado_revision: "generado"
---

# Tipo Activ Form Modificar

Devuelve el HTML del formulario para modificar/eliminar un tipo de actividad existente. Portado del case `form_modificar` del dispatcher legacy.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/tipo_activ_form_modificar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/tipo_activ_form_modificar.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Devuelve el HTML del formulario para modificar/eliminar un tipo de actividad existente.

## Casos De Uso

- `src\actividades\application\TipoActivFormModificar`

## Frontend Relacionado

- `frontend/actividades/controller/tipo_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.