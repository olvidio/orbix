---
id: "procesos.procesos_get"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/procesos_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/procesos_get.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/procesos_get.php", "frontend/procesos/controller/procesos_get_listado.php", "frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosGet"]
tags: ["procesos", "get"]
estado_revision: "generado"
---

# Procesos Get

Caso de uso: devuelve la estructura de padres/hijos del arbol de fases del proceso filtrando segun el sfsv/role del usuario. Retorna un array donde cada clave es el id de fase padre (0 = raiz) y cada valor es una lista de ['id', 'nom']. El HTML del árbol lo genera {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/procesos_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/procesos_get.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\procesos\application\ProcesosGet`

## Frontend Relacionado

- `frontend/procesos/controller/procesos_get.php`
- `frontend/procesos/controller/procesos_get_listado.php`
- `frontend/procesos/controller/procesos_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.