---
id: "procesos.tipo_activ_proceso_asignar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/tipo_activ_proceso_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php"
entrada: ["post.id_tipo_activ:integer", "post.id_tipo_proceso:integer", "post.propio:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["tipo de actividad no encontrado", "hay un error, no se ha guardado el proceso"]
frontend_referencias: ["frontend/procesos/controller/tipo_activ_proceso.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoAsignar"]
tags: ["procesos", "tipo", "activ", "proceso", "asignar"]
estado_revision: "generado"
---

# Tipo Activ Proceso Asignar

Caso de uso: asigna id_tipo_proceso al tipo de actividad (propio / no-propio).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/tipo_activ_proceso_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `integer` | application | No | application |
| `id_tipo_proceso` | `integer` | application | No | application |
| `propio` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `tipo de actividad no encontrado`
- `hay un error, no se ha guardado el proceso`

## Casos De Uso

- `src\procesos\application\TipoActivProcesoAsignar`

## Frontend Relacionado

- `frontend/procesos/controller/tipo_activ_proceso.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.