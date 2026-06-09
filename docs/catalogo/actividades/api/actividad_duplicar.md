---
id: "actividades.actividad_duplicar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_duplicar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_duplicar.php"
entrada: ["post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se ha seleccionado ninguna actividad", "actividad no encontrada", "no se puede duplicar actividades que no sean de la propia dl"]
frontend_referencias: []
casos_uso: ["src\\actividades\\application\\ActividadDuplicar"]
tags: ["actividades", "actividad", "duplicar"]
estado_revision: "generado"
---

# Actividad Duplicar

Endpoint backend AJAX: duplica la primera actividad seleccionada dentro de la propia delegacion (o de la sf si el usuario tiene permiso `des`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_duplicar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_duplicar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `des`

## Errores conocidos

- `no se ha seleccionado ninguna actividad`
- `actividad no encontrada`
- `no se puede duplicar actividades que no sean de la propia dl`

## Casos De Uso

- `src\actividades\application\ActividadDuplicar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.