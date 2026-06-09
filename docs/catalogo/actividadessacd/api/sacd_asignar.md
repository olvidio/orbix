---
id: "actividadessacd.sacd_asignar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacd_asignar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer"]
entrada_obligatoria: ["id_activ", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_nom", "No puede haber tantos cargos de sacd en una actividad", "hay un error, no se ha guardado el cargo", "hay un error, no se ha guardado la asistencia"]
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/controller/asignar_sacd_auto.php", "frontend/actividadessacd/view/asignar_sacd_auto.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdAsignar"]
tags: ["actividadessacd", "sacd", "asignar"]
estado_revision: "generado"
---

# Sacd Asignar

Endpoint backend: asigna un sacd a una actividad (y, si es sv, tambien crea la asistencia).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/sacd_asignar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Si | controller+application |
| `id_nom` | `integer` | controller+application | Si | controller+application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan parametros id_activ / id_nom`
- `No puede haber tantos cargos de sacd en una actividad`
- `hay un error, no se ha guardado el cargo`
- `hay un error, no se ha guardado la asistencia`

## Casos De Uso

- `src\actividadessacd\application\SacdAsignar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php`
- `frontend/actividadessacd/controller/asignar_sacd_auto.php`
- `frontend/actividadessacd/view/asignar_sacd_auto.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.