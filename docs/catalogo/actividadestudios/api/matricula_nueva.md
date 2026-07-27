---
id: "actividadestudios.matricula_nueva"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matricula_nueva"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matricula_nueva.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_nivel:integer", "post.id_nom:integer", "post.id_pau:integer", "post.id_preceptor:integer", "post.id_situacion:integer", "post.preceptor:string"]
entrada_obligatoria: ["id_activ", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ o id_nom", "no encuentro asignatura para ese nivel", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaNueva"]
tags: ["actividadestudios", "matricula", "nueva"]
estado_revision: "revisado"
---

# Matricula Nueva

Crea una matrícula (asignatura de una persona en una actividad) y ajusta los dossiers 1303
(persona) y 3103 (actividad) más la asignatura impartida (`ActividadAsignatura`). Sustituye al
case `nuevo` de `update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta o actualización de la matrícula `(id_activ, id_asignatura, id_nom)`. Si ya existe, sobrescribe
nivel, situación, preceptor e id_preceptor; si no, crea el registro.

**Casos particulares**

- **Alias `id_pau` → `id_nom`:** lee primero `id_pau`; si es ≤ 0, usa `id_nom`.
- **`id_asignatura === 1`:** resuelve la asignatura real por `id_nivel`
  (`getAsignaturas(['id_nivel' => …])`); si no hay, error `no encuentro asignatura para ese nivel`.
- **Preceptor:** `''` / `'f'` → `null`; cualquier otro valor no vacío → `true`.
- **Dossiers:** abre (crea si falta) el dossier 1303 de la persona; si existe el 3103 de la
  actividad, lo cierra.
- **`ActividadAsignatura`:** si no hay vínculo actividad–asignatura, lo crea; con preceptor verdadero
  (`isTrue`) pone `id_profesor = id_preceptor` y `tipo = 'p'`; si no, `tipo = ''`.

## Endpoint

- URL: `/src/actividadestudios/matricula_nueva`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_nueva.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Actividad |
| `id_nom` / `id_pau` | `integer` | application | Sí | Persona; alias `id_pau` → `id_nom` |
| `id_asignatura` | `integer` | application | No | Si es `1`, se resuelve por `id_nivel` |
| `id_nivel` | `integer` | application | Condicional | Obligatorio efectivo cuando `id_asignatura === 1` |
| `id_situacion` | `integer` | application | No | Situación de la matrícula |
| `preceptor` | `string` | application | No | `''`/`'f'` → null; resto → true |
| `id_preceptor` | `integer` | application | No | Preceptor; también profesor si crea AA con tipo `p` |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Inserta/actualiza `Matricula`.
- Abre dossier 1303 (persona); cierra dossier 3103 (actividad) si existe.
- Crea `ActividadAsignatura` si no había vínculo (tipo `'p'` si preceptor).

## Errores conocidos

- `falta id_activ o id_nom`
- `no encuentro asignatura para ese nivel`
- `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`form_matriculas_de_una_persona.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\MatriculaNueva`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php` (URL en payload
  `url_matricula_nueva`).
