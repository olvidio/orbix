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
frontend_referencias: []
casos_uso: ["src\\actividades\\application\\BorrarActividad"]
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
| `id_activ` | `integer` | controller | No | controller |
| `sel` | `array` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Servicio de aplicacion para eliminar (o marcar como borrable) una actividad.
- - Si la actividad es de la propia dl y esta en PROYECTO, se elimina fisicamente.
- - Si es de otra dl y esta importada (id_tabla='dl'), se elimina de Importada.

## Casos De Uso

- `src\actividades\application\BorrarActividad`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.