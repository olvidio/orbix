---
id: "pasarela.contribucion_reserva_excepcion_guardar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/contribucion_reserva_excepcion_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_excepcion_guardar.php"
entrada:
  - "post.id_tipo_activ:string"
  - "post.valor:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Falta id_tipo_activ"
  - "Falta valor de contribución"
  - "Debe ser un numero entero del 1 al 100"
frontend_referencias:
  - "frontend\/pasarela\/controller\/contribucion_reserva_ajax.php"
casos_uso: ["src\pasarela\application\ContribucionReservaExcepcionGuardar"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Contribucion Reserva Excepcion Guardar

Alta/edición de excepción de contribución reserva por tipo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Campo POST `valor` = porcentaje.

## Endpoint

- URL: `/src/pasarela/contribucion_reserva_excepcion_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/contribucion_reserva_excepcion_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |
| `valor` | `string` | controller | No | |


## Salida

- Éxito: `data: "ok"`.

## Errores conocidos

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ContribucionReservaExcepcionGuardar`

## Frontend Relacionado

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`