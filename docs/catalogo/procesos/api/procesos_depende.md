---
id: "procesos.procesos_depende"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_depende"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_depende.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "procesos_ProcesosDependeData"
respuesta_data: ["opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_ver.php"]
casos_uso: ["src\\procesos\\application\\ProcesosDepende"]
tags: ["procesos", "depende"]
estado_revision: "generado"
---

# Procesos Depende

Caso de uso: devuelve las opciones disponibles para el desplegable de tareas dependientes de la fase indicada (usado al cambiar de fase o fase_previa en el formulario procesos_ver). Respuesta JSON con `opciones` (value => label). El frontend inyecta los `<option>` en el `<select>` destino indicado por `acc`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_depende`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_depende.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `procesos_ProcesosDependeData`):
  - `opciones` (`array`)

## Casos De Uso

- `src\procesos\application\ProcesosDepende`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.