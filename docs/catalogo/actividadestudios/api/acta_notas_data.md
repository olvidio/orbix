---
id: "actividadestudios.acta_notas_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/acta_notas_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_data.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer"]
entrada_obligatoria: ["id_activ", "id_asignatura"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadestudios_ActaNotasDataData"
respuesta_data: ["msg_err:string", "permiso:integer", "nom_activ:string", "matriculados:integer", "matriculas_rows:list<array{nom: string, id_nom: int, nota_num: mixed, nota_max: mixed, preceptor: bool, acta: mixed}>", "notas:string", "despl_actas_opciones:array", "acta_principal:string", "acta_notas_a_actas:list<string>", "acta_txt_cursada:string"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasData"]
tags: ["actividadestudios", "acta", "notas", "data"]
estado_revision: "revisado"
---

# Acta Notas Data

Prepara el acta de notas de una asignatura de una actividad: matriculados con sus notas, actas
existentes y permiso de edición según la DL propietaria. Respalda `acta_notas`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para la asignatura `id_asignatura` de la actividad `id_activ`:

- Resuelve el `permiso` comparando la DL de la sesión con la DL del esquema de la matrícula
  (`3` = puede editar si coincide, `1` = solo lectura).
- Lista los matriculados (nombre, nota, nota máxima, preceptor, acta) ordenados sin acentos.
- Recupera las actas existentes y arma el desplegable (`despl_actas_opciones`), marca `notas`
  (`nuevo`/`acta`) y el acta principal si solo hay una.

Si la asignatura no está en la actividad, devuelve payload vacío con `msg_err` y `permiso=1`.

## Endpoint

- URL: `/src/actividadestudios/acta_notas_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/acta_notas_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | Sí | Actividad |
| `id_asignatura` | `integer` | controller+application | Sí | Asignatura del acta |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadestudios_ActaNotasDataData`):
  - `msg_err` (`string`): avisos (asignatura no encontrada, personas no encontradas).
  - `permiso` (`integer`): `3` (editar) / `1` (solo lectura) según DL propietaria.
  - `nom_activ` (`string`), `matriculados` (`integer`).
  - `matriculas_rows` (`list`): `{nom, id_nom, nota_num, nota_max, preceptor, acta}`.
  - `notas` (`string`): `nuevo` o `acta`.
  - `despl_actas_opciones` (`array`), `acta_principal` (`string`), `acta_notas_a_actas` (`list`).
  - `acta_txt_cursada` (`string`): etiqueta de la situación *cursada*.

## Errores conocidos

- Aviso `no encuentro la asignatura en la actividad` (devuelto en `msg_err` con payload vacío).

## Permisos

- El caso de uso calcula un flag `permiso` (3/1) comparando la DL de la sesión (`OrbixRuntime::miDelef`)
  con la DL del esquema de la matrícula; determina si el acta es editable. La autorización de oficina
  se completa en el frontend (`acta_notas.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\ActaNotasData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/acta_notas.php`