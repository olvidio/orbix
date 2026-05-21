---
id: "actividadessacd.sacds_disponibles_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacds_disponibles_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacds_disponibles_data.php"
entrada: ["post.id_activ:integer", "post.seleccion:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdsDisponiblesData"]
tags: ["actividadessacd", "sacds", "disponibles", "data"]
estado_revision: "generado"
---

# Sacds Disponibles Data

Endpoint backend: devuelve los sacd candidatos para asignar a una actividad (sacd del centro encargado + sacd globales segun bitmask `seleccion`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/sacds_disponibles_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacds_disponibles_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | controller+application |
| `seleccion` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadessacd\application\SacdsDisponiblesData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.