---
id: "dbextern.sincro_trasladar_a"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_trasladar_a"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar_a.php"
entrada: ["post.dl:string", "post.id_nom_orbix:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["No se encontró la delegación destino", "Este traslado debe hacerse desde el dossier de traslados", "Error al trasladar"]
frontend_referencias: ["frontend/dbextern/controller/ver_orbix_otradl.php"]
casos_uso: ["src\\dbextern\\application\\TrasladarPersonaUseCase"]
tags: ["dbextern", "sincro", "trasladar", "a"]
estado_revision: "revisado"
---

# Sincro Trasladar A

Traslada una persona Aquinate activa hacia la DL donde está su correspondencia BDU (punto 7).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Invoca `TrasladarPersonaUseCase::trasladarA`: situación `L`, esquema destino = DL de la fila BDU.
Si la región destino ≠ región actual, rechaza y pide usar el dossier de traslados.

## Endpoint

- URL: `/src/dbextern/sincro_trasladar_a`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_trasladar_a.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_orbix` | `integer` | controller | Sí | |
| `tipo_persona` | `string` | controller | Sí | |
| `dl` | `string` | controller | Sí | DL destino (formato del listado: `dlNN` o prefijo sin `cr`) |

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data` con resultado de `Trasladar::trasladar()`.
- Error: `success: false`, `mensaje`.

## Errores conocidos

- `No se encontró la delegación destino`
- `Este traslado debe hacerse desde el dossier de traslados` (+ línea sobre campo situación)
- `Error al trasladar` (fallback)
- Otros mensajes del dominio `Trasladar`

## Permisos

- HashFront en `ver_orbix_otradl.phtml`.

## Casos De Uso

- `src\dbextern\application\TrasladarPersonaUseCase` (método `trasladarA`)

## Frontend Relacionado

- `frontend/dbextern/controller/ver_orbix_otradl.php`
