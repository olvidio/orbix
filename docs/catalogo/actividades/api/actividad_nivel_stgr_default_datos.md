---
id: "actividades.actividad_nivel_stgr_default_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_nivel_stgr_default_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_nivel_stgr_default_datos.php"
entrada: ["post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_ver.php"]
casos_uso: ["src\\actividades\\application\\ActividadVerDatos"]
tags: ["actividades", "actividad", "nivel", "stgr", "default", "datos"]
estado_revision: "generado"
---

# Actividad Nivel Stgr Default Datos

Nivel STGR por defecto según id_tipo_activ ({

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_nivel_stgr_default_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nivel_stgr_default_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ActividadVerDatos`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.