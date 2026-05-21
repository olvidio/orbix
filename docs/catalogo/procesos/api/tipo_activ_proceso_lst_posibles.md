---
id: "procesos.tipo_activ_proceso_lst_posibles"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/tipo_activ_proceso_lst_posibles"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lst_posibles.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/tipo_activ_proceso.php", "frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php"]
casos_uso: ["src\\procesos\\application\\TipoActivProcesoLstPosibles"]
tags: ["procesos", "tipo", "activ", "proceso", "lst", "posibles"]
estado_revision: "generado"
---

# Tipo Activ Proceso Lst Posibles

Caso de uso: devuelve la lista de procesos posibles que el usuario puede asignar a un id_tipo_activ concreto, como estructura. El frontend se encarga de la mini-tabla HTML clickable.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/tipo_activ_proceso_lst_posibles`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/tipo_activ_proceso_lst_posibles.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\procesos\application\TipoActivProcesoLstPosibles`

## Frontend Relacionado

- `frontend/procesos/controller/tipo_activ_proceso.php`
- `frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.