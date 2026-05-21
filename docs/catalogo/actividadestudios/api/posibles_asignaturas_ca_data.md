---
id: "actividadestudios.posibles_asignaturas_ca_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/posibles_asignaturas_ca_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/posibles_asignaturas_ca_data.php"
entrada: ["post.id_activ:mixed", "post.nom_activ:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_PosiblesAsignaturasCaDataData"
respuesta_data: ["nom_activ:string", "aAsignaturas_alumnos:list<array{nom_asignatura: string, id_asignatura: int, posibles_alumnos: int, aNombresAlumnos: list<string>}>", "a_alumnos_fin_c:list<array{apellidos_nombre: string, asignaturas: mixed}>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/posibles_asignaturas_ca.php"]
casos_uso: ["src\\actividadestudios\\application\\PosiblesAsignaturasCaData"]
tags: ["actividadestudios", "posibles", "asignaturas", "ca", "data"]
estado_revision: "generado"
---

# Posibles Asignaturas Ca Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadestudios/posibles_asignaturas_ca_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/posibles_asignaturas_ca_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `mixed` | controller | No | controller |
| `nom_activ` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `actividadestudios_PosiblesAsignaturasCaDataData`):
  - `nom_activ` (`string`)
  - `aAsignaturas_alumnos` (`list<array{nom_asignatura: string, id_asignatura: int, posibles_alumnos: int, aNombresAlumnos: list<string>}>`)
  - `a_alumnos_fin_c` (`list<array{apellidos_nombre: string, asignaturas: mixed}>`)

## Casos De Uso

- `src\actividadestudios\application\PosiblesAsignaturasCaData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/posibles_asignaturas_ca.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.