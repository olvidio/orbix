---
id: "zonassacd.zona_ctr_ajax"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_ctr_ajax"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_ajax.php"
entrada: []
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["zonassacd", "zona", "ctr", "ajax"]
estado_revision: "revisado"
errores: []
---

# Zona Ctr Ajax

Ruta muerta: registrada en routes.php pero sin controller. Sustituida por zona_ctr_lista y zona_ctr_update.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Ruta muerta: registrada en routes.php pero sin controller. Sustituida por zona_ctr_lista y zona_ctr_update.

## Endpoint

- URL: `/src/zonassacd/zona_ctr_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_ctr_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Sin salida estándar (ruta muerta o pendiente).

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Sin control de permisos propio en el caso de uso; la autorización se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- _(ninguno — ruta muerta)_

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
