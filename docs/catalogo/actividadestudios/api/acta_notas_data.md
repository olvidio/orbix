---
id: "actividadestudios.acta_notas_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/acta_notas_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_data.php"
entrada: ["post.id_activ:mixed", "post.id_asignatura:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_ActaNotasDataData"
respuesta_data: ["msg_err:string", "permiso:integer", "nom_activ:string", "matriculados:integer", "matriculas_rows:list<array{nom: string, id_nom: int, nota_num: mixed, nota_max: mixed, preceptor: bool, acta: mixed}>", "notas:string", "despl_actas_opciones:array", "acta_principal:string", "acta_notas_a_actas:list<string>", "acta_txt_cursada:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasData"]
tags: ["actividadestudios", "acta", "notas", "data"]
estado_revision: "generado"
---

# Acta Notas Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/acta_notas_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `mixed` | controller | No | controller |
| `id_asignatura` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_ActaNotasDataData`):
  - `msg_err` (`string`)
  - `permiso` (`integer`)
  - `nom_activ` (`string`)
  - `matriculados` (`integer`)
  - `matriculas_rows` (`list<array{nom: string, id_nom: int, nota_num: mixed, nota_max: mixed, preceptor: bool, acta: mixed}>`)
  - `notas` (`string`)
  - `despl_actas_opciones` (`array`)
  - `acta_principal` (`string`)
  - `acta_notas_a_actas` (`list<string>`)
  - `acta_txt_cursada` (`string`)

## Casos De Uso

- `src\actividadestudios\application\ActaNotasData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/acta_notas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.