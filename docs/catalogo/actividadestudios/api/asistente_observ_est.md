---
id: "actividadestudios.asistente_observ_est"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/asistente_observ_est"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ_est.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.observ_est:string"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ o id_nom", "no encuentro al asistente", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml"]
casos_uso: ["src\\actividadestudios\\application\\AsistenteObservEst"]
tags: ["actividadestudios", "asistente", "observ", "est"]
estado_revision: "revisado"
---

# Asistente Observ Est

Guarda el texto `observ_est` de un Asistente (persona en una actividad de estudios). Sustituye al
case `observ_est` de `update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Localiza el asistente `(id_activ, id_nom)` y guarda `observ_est` (observación de estudios).

**Casos particulares**

- **Alias de persona:** lee primero `id_pau`; si es ≤ 0, usa `id_nom`.

## Endpoint

- URL: `/src/actividadestudios/asistente_observ_est`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ_est.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Actividad |
| `id_pau` | `integer` | application | Condicional | Alias de `id_nom` (prioridad) |
| `id_nom` | `integer` | application | Condicional | Persona; se usa si `id_pau` ≤ 0 |
| `observ_est` | `string` | application | No | Observación de estudios |

El controller pasa `$_POST` completo al caso de uso. Obligatorio: `id_activ` y (`id_pau` o `id_nom`).

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse` si `data` es objeto).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Actualiza el campo `observ_est` del Asistente.

## Errores conocidos

- `falta id_activ o id_nom`
- `no encuentro al asistente`
- `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\AsistenteObservEst`

## Frontend Relacionado

- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml` (URL en payload
  `url_asistente_observ_est`).
