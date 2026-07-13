---
id: "profesores.lista_por_departamentos"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/lista_por_departamentos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/profesores/infrastructure/ui/http/controllers/lista_por_departamentos.php"
entrada: ["post.dl:array", "post.filtro:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_ListaPorDepartamentosData"
respuesta_data: ["modo:string", "rstgr:boolean", "a_checked:array", "a_delegaciones:array", "aClaustro:array"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/lista_por_departamentos.php"]
casos_uso: ["src\\profesores\\application\\ListaPorDepartamentos"]
tags: ["profesores", "lista", "por", "departamentos", "claustro"]
estado_revision: "revisado"
---

# Lista Por Departamentos

Claustro STGR agrupado por departamento: directores y profesores por tipo, o formulario de filtro por
delegación en ámbito regional.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/lista_por_departamentos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/profesores/infrastructure/ui/http/controllers/lista_por_departamentos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `array` | controller | No | Códigos de delegación seleccionados (filtro RSTGR) |
| `filtro` | `integer` | controller | No | `1` tras aplicar filtro; sin `1` en RSTGR devuelve pantalla de filtro |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Rama `modo=filtro` (RSTGR, `filtro !== 1`): `a_checked`, `a_delegaciones` para checkboxes.
- Rama `modo=lista`: `aClaustro` — array de departamentos con `profesores` (clave `director` +
  cada tipo de profesor → personas ordenadas `ap_orden` → `dl` → texto «nombre (centro)»).
- `rstgr`: indica si el ámbito es regional.

## Objetivo funcional

Dos ramas: en RSTGR sin filtro aplicado muestra selector de delegaciones; con filtro o en delegación
local lista el claustro vigente (solo personas situación `A`, nombramientos sin cese).

## Permisos

- Sin `perm_*` en caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\profesores\application\ListaPorDepartamentos`

## Frontend Relacionado

- `frontend/profesores/controller/lista_por_departamentos.php` — si `modo=filtro` renderiza
  `dl_rstgr_que.html.twig`; si `modo=lista` renderiza `lista_por_departamentos.phtml`.
- Linaje: `apps/profesores/controller/lista_por_departamentos.php`.
