---
id: "pasarela.activacion_excepcion_eliminar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/activacion_excepcion_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/activacion_excepcion_eliminar.php"
entrada:
  - "post.id_tipo_activ:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Falta id_tipo_activ"
frontend_referencias:
  - "frontend\/pasarela\/controller\/activacion_ajax.php"
casos_uso: ["src\pasarela\application\ActivacionExcepcionEliminar"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Activacion Excepcion Eliminar

Elimina una excepción de `fecha_activacion` para un tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Borra la fila de excepción en `pasarela_dl`.

## Endpoint

- URL: `/src/pasarela/activacion_excepcion_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_excepcion_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |


## Salida

- Éxito: `data: "ok"`.

## Errores conocidos

- `Falta id_tipo_activ`

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ActivacionExcepcionEliminar`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`