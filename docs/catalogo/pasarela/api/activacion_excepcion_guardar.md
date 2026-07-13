---
id: "pasarela.activacion_excepcion_guardar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/activacion_excepcion_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/activacion_excepcion_guardar.php"
entrada:
  - "post.id_tipo_activ:string"
  - "post.valor:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Falta id_tipo_activ"
  - "Falta valor de activación"
frontend_referencias:
  - "frontend\/pasarela\/controller\/activacion_ajax.php"
casos_uso: ["src\pasarela\application\ActivacionExcepcionGuardar"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Activacion Excepcion Guardar

Alta o edición de una excepción de `fecha_activacion` para un `id_tipo_activ`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

El campo POST `valor` recibe la activación concreta (`activacion` en el formulario frontend).

## Endpoint

- URL: `/src/pasarela/activacion_excepcion_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_excepcion_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |
| `valor` | `string` | controller | No | |


## Salida

- Éxito: `data: "ok"`.

## Errores conocidos

- `Falta id_tipo_activ`
- `Falta valor de activación`

## Permisos

Sin control en el caso de uso; autorización en frontend.

Notas: Alias: el formulario envía `activacion` pero el controller mapea a `valor`.

## Casos De Uso

- `src\pasarela\application\ActivacionExcepcionGuardar`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`