---
id: "actividadestudios.asistente_plan_est_ok"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/asistente_plan_est_ok"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/asistente_plan_est_ok.php"
entrada: ["post.est_ok:string", "post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ o id_nom", "no encuentro al asistente", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml"]
casos_uso: ["src\\actividadestudios\\application\\AsistentePlanEstOk"]
tags: ["actividadestudios", "asistente", "plan", "est", "ok"]
estado_revision: "revisado"
---

# Asistente Plan Est Ok

Marca el flag `est_ok` (plan de estudios confirmado) de un Asistente. Sustituye al case `plan` de
`update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Localiza el asistente `(id_activ, id_nom)` y pone `est_ok` según `isTrue(est_ok)`.

**Casos particulares**

- **Alias de persona:** lee primero `id_pau`; si es ≤ 0, usa `id_nom`.
- **Valor truthy:** `est_ok` se interpreta con `FuncTablasSupport::isTrue` (no basta con string
  no vacío arbitrario).

## Endpoint

- URL: `/src/actividadestudios/asistente_plan_est_ok`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_plan_est_ok.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Actividad |
| `id_pau` | `integer` | application | Condicional | Alias de `id_nom` (prioridad) |
| `id_nom` | `integer` | application | Condicional | Persona; se usa si `id_pau` ≤ 0 |
| `est_ok` | `string` | application | No | Flag plan confirmado (`isTrue`) |

El controller pasa `$_POST` completo al caso de uso. Obligatorio: `id_activ` y (`id_pau` o `id_nom`).

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse` si `data` es objeto).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Actualiza el flag `est_ok` del Asistente.

## Errores conocidos

- `falta id_activ o id_nom`
- `no encuentro al asistente`
- `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\AsistentePlanEstOk`

## Frontend Relacionado

- `frontend/actividadestudios/view/select_matriculas_de_una_persona.phtml` (URL en payload
  `url_asistente_plan_est_ok`).
