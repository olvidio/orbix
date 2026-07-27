---
id: "actividadestudios.matricula_editar"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/matricula_editar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/matricula_editar.php"
entrada: ["post.id_activ:integer", "post.id_asignatura:integer", "post.id_nivel:integer", "post.id_nom:integer", "post.id_pau:integer", "post.id_preceptor:integer", "post.id_situacion:integer", "post.preceptor:string"]
entrada_obligatoria: ["id_activ", "id_asignatura", "id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan claves de la matricula", "no encuentro la matricula", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaEditar"]
tags: ["actividadestudios", "matricula", "editar"]
estado_revision: "revisado"
---

# Matricula Editar

Edita una matrícula existente (`id_asignatura`, `id_nivel`, `id_situacion`, `preceptor`,
`id_preceptor`). Sustituye al case `editar` de `update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Localiza la matrícula por `(id_activ, id_asignatura, id_nom)` y actualiza nivel, situación,
preceptor e id_preceptor. No toca dossiers ni `ActividadAsignatura`.

**Casos particulares**

- **Alias `id_pau` → `id_nom`:** lee primero `id_pau`; si es ≤ 0, usa `id_nom`.
- **Preceptor:** se guarda con `FuncTablasSupport::isTrue($preceptor)` (`'t'`/`true`/bool → bool;
  fallo de parseo → `null`).
- **Claves:** si `id_activ`, `id_nom` o `id_asignatura` ≤ 0 → `faltan claves de la matricula`.
- **No encontrada:** `findById` null → `no encuentro la matricula`.

## Endpoint

- URL: `/src/actividadestudios/matricula_editar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/matricula_editar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Parte de la PK |
| `id_asignatura` | `integer` | application | Sí | Parte de la PK |
| `id_nom` / `id_pau` | `integer` | application | Sí | Persona; alias `id_pau` → `id_nom` |
| `id_nivel` | `integer` | application | No | Nivel |
| `id_situacion` | `integer` | application | No | Situación |
| `preceptor` | `string` | application | No | Via `isTrue()` |
| `id_preceptor` | `integer` | application | No | Id del preceptor |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan claves de la matricula`
- `no encuentro la matricula`
- `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`form_matriculas_de_una_persona.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\MatriculaEditar`

## Frontend Relacionado

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php` (URL en payload
  `url_matricula_editar`).
