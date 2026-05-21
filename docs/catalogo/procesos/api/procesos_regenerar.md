---
id: "procesos.procesos_regenerar"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_regenerar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_regenerar.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosRegenerar"]
tags: ["procesos", "regenerar"]
estado_revision: "generado"
---

# Procesos Regenerar

Caso de uso: regenera las tareas del proceso a partir de las fases definidas en `tareas_proceso`, eliminando las sobrantes.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_regenerar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_regenerar.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Caso de uso: regenera las tareas del proceso a partir de las fases definidas en `tareas_proceso`, eliminando las sobrantes.

## Casos De Uso

- `src\procesos\application\ProcesosRegenerar`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.