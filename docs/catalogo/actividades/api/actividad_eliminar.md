---
id: "actividades.actividad_eliminar"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_eliminar.php"
entrada: ["post.id_activ:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["actividad no encontrada", "sesión de permisos no disponible", "No tiene permiso para borrar esta actividad"]
frontend_referencias: []
casos_uso: ["src\\actividades\\application\\ActividadEliminar"]
tags: ["actividades", "actividad", "eliminar"]
estado_revision: "generado"
---

# Actividad Eliminar

Endpoint backend AJAX: elimina las actividades indicadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `sel` | `array` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina actividades indicadas por selección masiva o por id único (planning).
- Sustituye la lógica del antiguo case `eliminar` de actividad_update.php.

## Errores conocidos

- `actividad no encontrada`
- `sesión de permisos no disponible`
- `No tiene permiso para borrar esta actividad`

## Casos De Uso

- `src\actividades\application\ActividadEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.