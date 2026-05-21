---
id: "actividades.actividad_ver_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_ver_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_ver_datos.php"
entrada: ["post.Bdl:string", "post.calc_tarifa_inicial:integer", "post.dl_org:string", "post.id_activ:integer", "post.id_repeticion:integer", "post.id_tipo_activ:string", "post.id_ubi:integer", "post.idioma:string", "post.isfsv:integer", "post.lugar_esp:string", "post.nivel_stgr:mixed", "post.tarifa:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php"]
casos_uso: ["src\\actividades\\application\\ActividadVerDatos"]
tags: ["actividades", "actividad", "ver", "datos"]
estado_revision: "generado"
---

# Actividad Ver Datos

Endpoint backend: devuelve los fragmentos HTML y valores auxiliares que necesita el formulario "ver/editar actividad" para renderizarse sin que el frontend acceda directamente a `src/`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_ver_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_ver_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `Bdl` | `string` | controller | No | controller |
| `calc_tarifa_inicial` | `integer` | controller | No | controller |
| `dl_org` | `string` | controller | No | controller |
| `id_activ` | `integer` | controller | No | controller |
| `id_repeticion` | `integer` | controller | No | controller |
| `id_tipo_activ` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `idioma` | `string` | controller | No | controller |
| `isfsv` | `integer` | controller | No | controller |
| `lugar_esp` | `string` | controller | No | controller |
| `nivel_stgr` | `mixed` | controller | No | controller |
| `tarifa` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ActividadVerDatos`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.