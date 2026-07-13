---
id: "pasarela.contribucion_reserva_default_guardar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_reserva_default_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_guardar.php"
entrada:
  - "post.default:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Falta valor por defecto"
  - "Debe ser un numero entero del 1 al 100"
frontend_referencias:
  - "frontend\/pasarela\/controller\/contribucion_reserva_ajax.php"
casos_uso: ["src\pasarela\application\ContribucionReservaDefaultGuardar"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Contribucion Reserva Default Guardar

Actualiza el porcentaje por defecto de contribución reserva.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Valida entero 0–100.

## Endpoint

- URL: `/src/pasarela/contribucion_reserva_default_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_default_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `default` | `string` | controller | No | |


## Salida

- Éxito: `data: "ok"`.

## Errores conocidos

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ContribucionReservaDefaultGuardar`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`