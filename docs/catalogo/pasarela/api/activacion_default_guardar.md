---
id: "pasarela.activacion_default_guardar"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/activacion_default_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/activacion_default_guardar.php"
entrada:
  - "post.default:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:
  - "Falta valor por defecto"
frontend_referencias:
  - "frontend\/pasarela\/controller\/activacion_ajax.php"
casos_uso: ["src\pasarela\application\ActivacionDefaultGuardar"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Activacion Default Guardar

Actualiza el valor por defecto del parámetro `fecha_activacion`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Persiste el default global (p. ej. «3 días», «5 días» o `upload`).

## Endpoint

- URL: `/src/pasarela/activacion_default_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/activacion_default_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `default` | `string` | controller | No | |


## Salida

- Éxito: `success: true`, `data: "ok"` (string vacío en el caso de uso).

## Errores conocidos

- `Falta valor por defecto`

## Permisos

Sin control en el caso de uso; autorización en frontend.

## Casos De Uso

- `src\pasarela\application\ActivacionDefaultGuardar`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`