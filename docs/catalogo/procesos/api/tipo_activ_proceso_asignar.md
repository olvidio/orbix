---
id: "procesos.tipo_activ_proceso_asignar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/tipo_activ_proceso_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado el proceso"]
frontend_referencias: ["frontend/procesos/controller/tipo_activ_proceso.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoAsignar"]
tags: ["procesos", "tipo", "activ", "proceso", "asignar"]
estado_revision: "generado"
---

# Tipo Activ Proceso Asignar

Caso de uso: asigna un id_tipo_proceso al tipo de actividad indicado, distinguiendo entre proceso propio (dl) o no-propio segun `propio`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/tipo_activ_proceso_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_asignar.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `hay un error, no se ha guardado el proceso`

## Casos De Uso

- `src\procesos\application\TipoActivProcesoAsignar`

## Frontend Relacionado

- `frontend/procesos/controller/tipo_activ_proceso.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.