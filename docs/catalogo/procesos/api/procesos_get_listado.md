---
id: "procesos.procesos_get_listado"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_get_listado"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_get_listado.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_get_listado.php"]
casos_uso: ["src\\procesos\\application\\ProcesosGetListado"]
tags: ["procesos", "get", "listado"]
estado_revision: "generado"
---

# Procesos Get Listado

Caso de uso: devuelve el listado (estructurado) de fases/tareas del proceso filtrando por sfsv/role. El render HTML se hace en el frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_get_listado`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_get_listado.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\procesos\application\ProcesosGetListado`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_get_listado.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.