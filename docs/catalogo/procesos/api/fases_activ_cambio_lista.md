---
id: "procesos.fases_activ_cambio_lista"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_lista.php"
entrada: ["post.accion:string", "post.dl_propia:string", "post.empiezamax:string", "post.empiezamin:string", "post.id_fase_nueva:string", "post.id_tipo_activ:string", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio_lista.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioLista"]
tags: ["procesos", "fases", "activ", "cambio", "lista"]
estado_revision: "generado"
---

# Fases Activ Cambio Lista

Caso de uso: datos estructurados para tabla de actividades candidatas a cambiar de fase.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `accion` | `string` | application | No | application |
| `dl_propia` | `string` | application | No | application |
| `empiezamax` | `string` | application | No | application |
| `empiezamin` | `string` | application | No | application |
| `id_fase_nueva` | `string` | application | No | application |
| `id_tipo_activ` | `string` | application | No | application |
| `periodo` | `string` | application | No | application |
| `year` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\procesos\application\FasesActivCambioLista`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.