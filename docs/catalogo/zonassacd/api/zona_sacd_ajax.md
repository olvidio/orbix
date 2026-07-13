---
id: "zonassacd.zona_sacd_ajax"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_ajax"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_ajax.php"
entrada: []
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["zonassacd", "zona", "sacd", "ajax"]
estado_revision: "revisado"
errores: []
---

# Zona Sacd Ajax

Ruta muerta: registrada en routes.php pero el controller no existe. Era dispatcher legacy (que=get_lista|update|get_lista_tot), sustituido por zona_sacd_lista, zona_sacd_update y zona_sacd_lista_tot.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Ruta muerta: registrada en routes.php pero el controller no existe. Era dispatcher legacy (que=get_lista|update|get_lista_tot), sustituido por zona_sacd_lista, zona_sacd_update y zona_sacd_lista_tot.

## Endpoint

- URL: `/src/zonassacd/zona_sacd_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_ajax.php`

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
