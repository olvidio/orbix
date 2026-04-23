# Actividadescentro — baseline de migracion

## Resumen del modulo

`actividadescentro` gestiona los **centros encargados** (organizadores) de una
actividad. Un centro encargado es la unidad `ubi` que organiza la actividad
(tabla `da_ctr_encargados` con clave `{id_activ, id_ubi}` + `num_orden` +
`encargo`). La pantalla permite:

1. Listar actividades (sg / sr / nagd / sssc / sfsg / sfsr / sfnagd) en un
   periodo y mostrar, para cada una, sus centros encargados actuales.
2. Asignar un centro nuevo desde un desplegable lateral (tipos de centros
   distintos segun el tipo de actividad).
3. Reordenar centros encargados (`+ prioridad` / `- prioridad`) o borrarlos.

La entidad `CentroEncargado` y su repositorio ya viven en
`src/actividadescentro/` (DDD completo: entity, VOs, contract, impl PG). Lo
que falta migrar es la capa HTTP (controladores) y la UI.

## Estado inicial (antes del refactor)

```
apps/actividadescentro/
├── controller/
│   ├── activ_ctr.php            (111 LOC) pantalla principal (filtros periodo)
│   └── activ_ctr_ajax.php       (457 LOC) dispatcher switch($Qque) con 10 ramas
├── model/
│   └── Info3010.php             DatosInfo dossier 3010 (sin cambios)
└── view/
    └── activ_ctr.html.twig      JS + form, renderiza respuesta HTML en #exportar

src/actividadescentro/
├── config/dependencies.php      (solo repo PG)
├── db/…                         (esquema PG)
├── domain/…                     CentroEncargado, VOs, contract
└── infrastructure/
    ├── persistence/postgresql/  PgCentroEncargadoRepository
    └── ui/http/                 (vacio, solo carpeta controllers)

frontend/actividadescentro/      (no existe)
```

No hay `src/actividadescentro/application/`, ni `routes.php`, ni endpoints
`/src/actividadescentro/*`.

## Ramas del dispatcher `activ_ctr_ajax.php`

El switch `$Qque` cubre 10 acciones:

| Rama | Tipo | Salida | Destino refactor |
|------|------|--------|------------------|
| `orden` + `num_orden=mas\|menos` | mutacion | `{que, txt, error}` | `centro_encargado_reordenar` |
| `orden` + `num_orden=borrar` | mutacion | `{que, txt, error}` | `centro_encargado_eliminar` |
| `get` | lectura (HTML inline `<td>…</td>`) | HTML | `centros_encargados_data` (JSON data + frontend pinta) |
| `nuevo_sg` | lectura (HTML tabla) | HTML | `centros_disponibles_data` (tipo=sg) |
| `nuevo_sr` | lectura (HTML tabla) | HTML | `centros_disponibles_data` (tipo=sr) |
| `nuevo_nagd` | lectura (HTML tabla) | HTML | `centros_disponibles_data` (tipo=nagd) |
| `nuevo_sssc` | lectura (HTML tabla) | HTML | `centros_disponibles_data` (tipo=sssc) |
| `nuevo_sfsg` | lectura (HTML tabla) | HTML | `centros_disponibles_data` (tipo=sfsg) |
| `nuevo_sfsr` | lectura (HTML tabla) | HTML | `centros_disponibles_data` (tipo=sfsr) |
| `nuevo_sfnagd` | lectura (HTML tabla) | HTML | `centros_disponibles_data` (tipo=sfnagd) |
| `asignar` | mutacion | texto plano error o vacio | `centro_encargado_asignar` |
| `lista_activ` | lectura (HTML tabla completa) | HTML | `lista_actividades_ctr_data` (JSON data; frontend monta tabla) |

La unificacion de `nuevo_<tipo>` en un solo endpoint parametrizado
`/src/actividadescentro/centros_disponibles_data?tipo=<tipo>` encaja en la
**excepcion tolerable** de `refactor.md` (dispatcher de lectura con ramas que
comparten contrato y viven como un use case tipado).

## Consumidores externos

- Menu metamenus (`log/menus/comun.sql`, `log/menus/apps.sql`,
  `proves/aux_metamenus.csv`, `documentacion/Documentacion_Obix/menus.csv`):
  apuntan a `apps/actividadescentro/controller/activ_ctr.php`.
- Referencias en docs (`documentacion/Documentacion_Obix/10. Supernumerarios`,
  `15. SR`, `20. Calendario`, `8. dre`, `actividadescentro/mapa_activ_ctr.md`).
- No hay consumidores JS/PHP externos del dispatcher `activ_ctr_ajax.php`.

## Violaciones de `refactor.md`

1. **Dispatcher `$Qque`** con 10 ramas mezclando mutaciones y lecturas.
2. **Mutaciones sin `ContestarJson`**: responden JSON a mano
   (`echo "{ \"que\": ... }"`) y el caso `asignar` responde texto plano.
3. **Backend `echo` de HTML**: las ramas `get`, `nuevo_*` y `lista_activ`
   construyen HTML en el controller, incluyendo onclick handlers y estructura
   de `<table>`.
4. **Vista en `apps/actividadescentro/view/`** (twig), no en `frontend/…/view/`.
5. **Controlador en `apps/`**, no `frontend/actividadescentro/`.
6. **Sin `src/actividadescentro/application/`**: la logica esta inline en el
   controlador `apps/…/activ_ctr_ajax.php`.
7. **Funcion suelta `ordena()`** definida en el controlador dispatcher: debe
   volverse use case tipado.
8. **Logica de negocio en `.phtml`/`.twig`**: el twig monta el `<form>` pero
   tambien compone JS con interpolaciones `{{ h_actualizar|raw }}` de hashes
   que cubren parametros de URLs del dispatcher. Tras el split, cada URL y
   hash se genera en el controller frontend y se pasa al phtml como string.

## Plan de migracion (un solo slice)

Tamaño moderado (1 controller + 1 ajax dispatcher + 1 twig + 1 Info3010).
Caben en un solo commit:

1. **`src/actividadescentro/application/`** — use cases:
   - `CentroEncargadoReordenar` (mutacion, sustituye `ordena()`)
   - `CentroEncargadoEliminar` (mutacion)
   - `CentroEncargadoAsignar` (mutacion)
   - `CentrosDisponiblesData` (lectura parametrizada por `tipo`)
   - `CentrosEncargadosData` (lectura: centros encargados de una actividad)
   - `ListaActividadesCtrData` (lectura: tabla principal actividades + ctrs)
2. **`src/actividadescentro/config/routes.php`** — registra 6 endpoints
   `/src/actividadescentro/<accion>` (POST).
3. **`src/actividadescentro/infrastructure/ui/http/controllers/`** — 6
   controllers finos que hacen `ContestarJson::enviar($err, $data)`.
4. **`frontend/actividadescentro/controller/activ_ctr.php`** — entrada del
   menu; construye hashes para cada URL AJAX, pasa al view.
5. **`frontend/actividadescentro/view/activ_ctr.phtml`** — migrado desde twig;
   JS JSON-aware (`dataType: 'json'`, helpers que construyen tablas desde los
   arrays devueltos por el backend), sin HTML desde el backend.
6. **Wrapper legacy** `apps/actividadescentro/controller/activ_ctr.php` →
   `require` al frontend. Comentario `// deprecado: usar frontend/...`.
7. **Borrar** `apps/actividadescentro/controller/activ_ctr_ajax.php` y
   `apps/actividadescentro/view/activ_ctr.html.twig`.
8. **Actualizar menus** `log/menus/*.sql`, `proves/aux_metamenus.csv`,
   `documentacion/Documentacion_Obix/menus.csv` a `frontend/actividadescentro/
   controller/activ_ctr.php`. Actualizar referencias en `documentacion/
   Documentacion_Obix/*.md`.
9. `php -l` en todos los ficheros nuevos o tocados.

## Estado final

```
src/actividadescentro/
├── application/
│   ├── CentroEncargadoAsignar.php
│   ├── CentroEncargadoEliminar.php
│   ├── CentroEncargadoReordenar.php
│   ├── CentrosDisponiblesData.php
│   ├── CentrosEncargadosData.php
│   └── ListaActividadesCtrData.php
├── config/
│   ├── dependencies.php
│   └── routes.php
├── db/…                                (sin cambios)
├── domain/…                            (sin cambios)
└── infrastructure/
    ├── persistence/…                   (sin cambios)
    └── ui/http/controllers/
        ├── centro_encargado_asignar.php
        ├── centro_encargado_eliminar.php
        ├── centro_encargado_reordenar.php
        ├── centros_disponibles_data.php
        ├── centros_encargados_data.php
        └── lista_actividades_ctr_data.php

frontend/actividadescentro/
├── controller/
│   └── activ_ctr.php                   (delgado, PostRequest + Hash + ViewNewPhtml)
└── view/
    └── activ_ctr.phtml                 (JSON-aware JS, helpers, sin <?= HTML pesado)

apps/actividadescentro/
├── controller/
│   └── activ_ctr.php                   (wrapper legacy → require frontend)
└── model/
    └── Info3010.php                    (sin cambios, dossier 3010)
```

## Contrato JSON por endpoint

### `/src/actividadescentro/centro_encargado_asignar`

POST `{id_activ:int, id_ubi:int}`. Crea o reasigna el `CentroEncargado`.
Calcula `num_orden = max(num_orden)+1`. `encargo = 'organizador'`.
Respuesta: `{success, mensaje, data:'ok'}`.

### `/src/actividadescentro/centro_encargado_reordenar`

POST `{id_activ:int, id_ubi:int, num_orden:'mas'|'menos'}`. Intercambia el
`num_orden` con el vecino superior/inferior segun la direccion. Respuesta:
`{success, mensaje, data:'ok'}`.

### `/src/actividadescentro/centro_encargado_eliminar`

POST `{id_activ:int, id_ubi:int}`. Elimina la fila `{id_activ, id_ubi}`.
Respuesta: `{success, mensaje, data:'ok'}`.

### `/src/actividadescentro/centros_encargados_data`

POST `{id_activ:int, id_tipo_activ:string, dl_org:string}`. Devuelve los
centros encargados actuales de la actividad junto con el flag
`permite_modificar` (segun `PermisosActividades`).

```json
{
  "id_activ": 123,
  "permite_modificar": true,
  "centros": [
    {"id_ubi": 111, "nombre_ubi": "Casa Foo"},
    {"id_ubi": 222, "nombre_ubi": "Casa Bar"}
  ]
}
```

### `/src/actividadescentro/centros_disponibles_data`

POST `{tipo:'sg'|'sr'|'nagd'|'sssc'|'sfsg'|'sfsr'|'sfnagd', id_activ:int,
inicio:string, fin:string, f_ini_act:string}`. Devuelve lista de centros
candidatos.

Para `tipo=sg` ademas inyecta `num_actividades_periodo` y `dif_dias` (proximas
actividades del centro respecto a `f_ini_act`); para los demas tipos solo
`{id_ubi, nombre_ubi}`.

```json
{
  "tipo": "sg",
  "id_activ": 123,
  "centros": [
    {"id_ubi": 111, "nombre_ubi": "Casa Foo", "num_actividades_periodo": 3, "dif_dias": " 12; -5; 30"}
  ]
}
```

### `/src/actividadescentro/lista_actividades_ctr_data`

POST filtros de periodo + tipo. Devuelve la tabla principal como array (no
HTML) ya ordenada. Incluye, por actividad, sus centros encargados y los flags
de permiso (`ver`, `modificar`, `crear`) para que el frontend decida que
renderizar.

```json
{
  "titulo": "listado de actividades sg",
  "inicio_iso": "2026-01-01",
  "fin_iso": "2026-12-31",
  "filas": [
    {
      "id_activ": 123,
      "nom_activ": "Actividad Foo",
      "f_ini": "01/06/2026",
      "f_fin": "05/06/2026",
      "perm_modificar_ctr": true,
      "perm_crear_ctr": true,
      "centros": [{"id_ubi": 111, "nombre_ubi": "Casa Foo"}]
    }
  ]
}
```

## Desviaciones respecto al legacy documentadas

- El dispatcher legacy responde a `orden=borrar` con una clave `error` en
  JSON (no usa `ContestarJson`). Se unifica al patron estandar `{success,
  mensaje}`; los JS consumidores se actualizan para leer `success` y pintar
  `mensaje` cuando procede.
- La rama `lista_activ` del dispatcher renderiza `<table>` inline (incluyendo
  los onclick `fnjs_cambiar_ctr` / `fnjs_nuevo_ctr`). En el refactor, esa
  misma tabla se construye en **PHP dentro del frontend controller**
  (`activ_ctr.php`) a partir del array devuelto por
  `lista_actividades_ctr_data`. El HTML desaparece del backend.
- La rama `asignar` del legacy responde con el texto del error del repo en
  texto plano y nada si ok. Se unifica a JSON.
- Las ramas `nuevo_<tipo>` devuelven `<table>` con `<tr>` y onclick
  `fnjs_asignar_ctr`. Tras el refactor, el backend devuelve JSON con el
  listado y el JS monta la tabla via un helper `fnjs_construir_tabla_ctrs`.
- `Info3010` no se toca en este slice: es metadata de dossier y no esta
  acoplado al dispatcher.

## Validacion

- `php -l` en los 6 use cases, 6 controllers HTTP, `routes.php`, controller
  frontend y vista.
- Compara con pantalla legacy: periodo actual/tot_any, tipo `sg`, debe mostrar
  el mismo numero de actividades y los mismos centros encargados.
- Prueba mutaciones: asignar un centro → aparece en la celda; `mas/menos
  prioridad` reordena; `borrar` elimina.
- `grep` final `apps/actividadescentro/controller/activ_ctr_ajax.php` → debe
  dar 0 resultados fuera de `languages/*.po*` (gettext cache se regenera).
