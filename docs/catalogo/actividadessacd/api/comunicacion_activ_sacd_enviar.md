---
id: "actividadessacd.comunicacion_activ_sacd_enviar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/comunicacion_activ_sacd_enviar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_enviar.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta determinar un periodo"]
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ComunicacionActividadesSacdEnviar"]
tags: ["actividadessacd", "comunicacion", "activ", "sacd", "enviar"]
estado_revision: "generado"
---

# Comunicacion Activ Sacd Enviar

Endpoint backend: encola mails de comunicacion de actividades a sacd.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/comunicacion_activ_sacd_enviar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_enviar.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `falta determinar un periodo`

## Casos De Uso

- `src\actividadessacd\application\ComunicacionActividadesSacdEnviar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`
- `frontend/actividadessacd/view/com_sacd_activ_periodo.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.