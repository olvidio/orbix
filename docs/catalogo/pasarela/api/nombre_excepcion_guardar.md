---
id: "pasarela.nombre_excepcion_guardar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/nombre_excepcion_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/nombre_excepcion_guardar.php"
entrada:
  - "post.id_tipo_activ:string"
  - "post.valor:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Falta id_tipo_activ"
  - "Falta nombre"
frontend_referencias:
  - "frontend\/pasarela\/controller\/nombre_ajax.php"
casos_uso: ["src\pasarela\application\NombreExcepcionGuardar"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Nombre Excepcion Guardar

Alta/edición del nombre de actividad para un tipo concreto.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Campo POST `valor` recibe `nombre_actividad` del formulario.

## Endpoint

- URL: `/src/pasarela/nombre_excepcion_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/nombre_excepcion_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |
| `valor` | `string` | controller | No | |


## Salida

- Éxito: `data: "ok"`.

## Errores conocidos

- `Falta id_tipo_activ`
- `Falta nombre`

## Permisos

Sin control en el caso de uso; autorización en frontend.

Notas: Alias: formulario envía `nombre_actividad` → controller mapea a `valor`.

## Casos De Uso

- `src\pasarela\application\NombreExcepcionGuardar`

## Frontend Relacionado

- `frontend/pasarela/controller/nombre_ajax.php`