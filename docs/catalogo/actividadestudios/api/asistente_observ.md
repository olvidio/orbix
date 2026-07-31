---
id: "actividadestudios.asistente_observ"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/asistente_observ"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ.php"
entrada: ["post.id_activ:integer", "post.id_nom:integer", "post.id_pau:integer", "post.observ:string"]
entrada_obligatoria: ["id_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta id_activ o id_nom", "no encuentro al asistente", "hay un error, no se ha guardado"]
frontend_referencias: []
casos_uso: ["src\\actividadestudios\\application\\AsistenteObserv"]
tags: ["actividadestudios", "asistente", "observ"]
estado_revision: "revisado"
---

# Asistente Observ

Guarda el texto `observ` de un Asistente. Sustituye al case `observ` de `update_3103.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Localiza el asistente `(id_activ, id_nom)` vía el repositorio adecuado
(`AsistenteActividadService::getRepoAsistente`) y guarda `observ`.

**Casos particulares**

- **Alias de persona:** lee primero `id_pau`; si es ≤ 0, usa `id_nom`.

## Endpoint

- URL: `/src/actividadestudios/asistente_observ`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/asistente_observ.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Sí | Actividad |
| `id_pau` | `integer` | application | Condicional | Alias de `id_nom` (prioridad) |
| `id_nom` | `integer` | application | Condicional | Persona; se usa si `id_pau` ≤ 0 |
| `observ` | `string` | application | No | Texto de observación |

El controller pasa `$_POST` completo al caso de uso. Obligatorio: `id_activ` y (`id_pau` o `id_nom`).

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse` si `data` es objeto).
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Actualiza el campo `observ` del Asistente.

## Errores conocidos

- `falta id_activ o id_nom`
- `no encuentro al asistente`
- `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadestudios\application\AsistenteObserv`

## Frontend Relacionado

- No hay referencia literal a esta URL en `frontend/actividadestudios/` (posible invocación vía
  payload `url_*` de otra pantalla o legacy). Endpoint hermano `asistente_observ_est` sí se usa
  desde `select_matriculas_de_una_persona`.
