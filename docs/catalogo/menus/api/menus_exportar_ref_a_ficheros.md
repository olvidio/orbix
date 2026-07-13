---
id: "menus.menus_exportar_ref_a_ficheros"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_exportar_ref_a_ficheros"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_exportar_ref_a_ficheros.php"
entrada: ["post.accion:string"]
entrada_obligatoria: ["accion"]
respuesta: "raw_response"
requiere_hashb: false
errores: []
frontend_referencias: []
casos_uso: []
tags: ["menus", "exportar", "ficheros", "ref"]
estado_revision: "revisado"
---

# Exportar/importar ref menús ↔ ficheros SQL

Operación de mantenimiento: genera ficheros COPY en `log/menus/` o ejecuta `psql` para volcar ref→BD pública.
**No devuelve JSON**; respuesta vacía o errores en `log/menus/menus.log`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- `accion=exportar`: COPY de `m0_modulos`, `m0_apps`, `aux_metamenus`, `ref_grupmenu`, `ref_grupmenu_rol`, `ref_menus` a SQL en `log/menus/`.
- `accion=importar`: ejecuta `tot_menus.sql` contra BD pública vía `psql` (requiere sudoers www-data).

## Entrada

| Campo | Valores |
|-------|---------|
| `accion` | `exportar` \| `importar` |

## Salida

- Sin envelope JSON. Importación: salida en `log/menus/menus.log`.

## Permisos

- Menú «importar desde ficheros» (operación de servidor).

## Frontend Relacionado

- Invocado desde menú sistema (legacy `menus_ficheros.php?accion=importar` en referencia).
