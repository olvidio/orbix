---
id: "dbextern.sincro_trasladar"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_trasladar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar.php"
entrada: ["post.dl:string", "post.id_nom_orbix:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["Error al trasladar"]
frontend_referencias: ["frontend/dbextern/controller/ver_traslados.php"]
casos_uso: ["src\\dbextern\\application\\TrasladarPersonaUseCase"]
tags: ["dbextern", "sincro", "trasladar"]
estado_revision: "revisado"
---

# Sincro Trasladar

Traslada a esta DL una persona que está activa en otra DL Orbix (punto 2).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Invoca `TrasladarPersonaUseCase::trasladar`: situación `L`, esquema destino = DL actual. El front
advierte que la fecha de traslado será hoy.

## Endpoint

- URL: `/src/dbextern/sincro_trasladar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_orbix` | `integer` | controller | Sí | |
| `tipo_persona` | `string` | controller | Sí | |
| `dl` | `string` | controller | Sí | DL Orbix origen (de la fila) |

## Salida

- Helper: `ContestarJson::enviar` (payload objeto en `data`; doble parse si string).
- Éxito: `success: true`, `data` con `{success: true, …}` del dominio `Trasladar`.
- Error: `success: false`, `mensaje` del dominio o `Error al trasladar`.

## Errores conocidos

- Mensajes del dominio `Trasladar::trasladar()` (vía `mensaje`)
- Fallback: `Error al trasladar`

## Permisos

- HashFront en `ver_traslados.phtml`.

## Casos De Uso

- `src\dbextern\application\TrasladarPersonaUseCase` (método `trasladar`)

## Frontend Relacionado

- `frontend/dbextern/controller/ver_traslados.php` → `fnjs_trasladar`
