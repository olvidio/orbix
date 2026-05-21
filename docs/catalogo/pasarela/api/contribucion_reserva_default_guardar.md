---
id: "pasarela.contribucion_reserva_default_guardar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_reserva_default_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_guardar.php"
entrada: ["post.default:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta valor por defecto", "Debe ser un numero entero del 1 al 100"]
frontend_referencias: ["frontend/pasarela/controller/contribucion_reserva_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionReservaDefaultGuardar"]
tags: ["pasarela", "contribucion", "reserva", "default", "guardar"]
estado_revision: "generado"
---

# Contribucion Reserva Default Guardar

Actualiza el valor por defecto del parámetro `contribucion_reserva`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/contribucion_reserva_default_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `default` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Casos De Uso

- `src\pasarela\application\ContribucionReservaDefaultGuardar`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.