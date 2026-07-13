---
id: "actividadestudios.e43_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/e43_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/e43_data.php"
entrada: ["post.id_nom:integer", "post.id_activ:integer", "post.append_blank_footer:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_E43CertificadoDataData"
respuesta_data: ["msg_err:string", "nom:string", "txt_nacimiento:string", "dl_origen:string", "dl_destino:string", "txt_actividad:string", "matriculas:integer", "aAsignaturasMatriculadas:list<array{nom_asignatura: mixed, nota: string, f_acta: string, acta: string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/e43.php"]
casos_uso: ["src\\actividadestudios\\application\\E43CertificadoData"]
tags: ["actividadestudios", "e43", "data"]
estado_revision: "revisado"
---

# E43 Data

Datos del certificado E43 de una persona en una actividad (pantalla e imprimible): datos personales,
DL origen/destino, descripción de la actividad y asignaturas matriculadas con su nota/acta.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para persona `id_nom` y actividad `id_activ`: resuelve nombre y datos de nacimiento, DL origen (la de
la sesión) y destino (la del alumno), descripción de la actividad (lugar + fechas) y, por cada
matrícula, la asignatura con su nota, fecha de acta y acta. Si `append_blank_footer` es truthy añade
una fila en blanco (para el pie del PDF). Si no encuentra persona o actividad, devuelve payload
parcial con `msg_err`.

## Endpoint

- URL: `/src/actividadestudios/e43_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/e43_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller+application | No | Persona destinataria del certificado |
| `id_activ` | `integer` | controller+application | No | Actividad del certificado |
| `append_blank_footer` | `string` | application | No | Si truthy, añade fila en blanco final (pie PDF) |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_E43CertificadoDataData`):
  - `msg_err` (`string`): avisos (persona/actividad no encontrada, sin matrículas).
  - `nom` (`string`), `txt_nacimiento` (`string`): datos personales.
  - `dl_origen` (`string`): DL de la sesión; `dl_destino` (`string`): DL del alumno.
  - `txt_actividad` (`string`): `lugar, f_ini-f_fin`.
  - `matriculas` (`integer`): número de matrículas.
  - `aAsignaturasMatriculadas` (`list`): `{nom_asignatura, nota, f_acta, acta}`.

## Errores conocidos

- Avisos no bloqueantes en `msg_err`: persona no encontrada, `No encuentro actividad con id: <id>`,
  `no hay ninguna matrícula de esta persona`.

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`e43.php`) y en `$_SESSION['oPerm']`. No inferir permisos concretos aquí.

## Casos De Uso

- `src\actividadestudios\application\E43CertificadoData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/e43.php`