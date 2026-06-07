# Actividadessacd — baseline de migracion

Documenta los slices del modulo `actividadessacd`. Cada slice mantiene un
vertical slice pequeño segun `refactor.md`.

- Slice 1: `activ_sacd` (filter + dispatcher ajax + twig).
- Slice 2: `asignar_sacd_auto` (formulario de confirmacion + mutacion).
- Slice 3: `com_sacd_txt` (editor de textos de comunicacion + dispatcher
  ajax con 2 ramas).
- Slice 4: `com_sacd_activ_periodo` + `com_sacd_activ` (lista de atencion
  de actividades por sacd + envio de mails a los sacd). Unifica ambas
  pantallas tras migrar el modelo `ComunicarActividadesSacd`.

---

## Slice 1: `activ_sacd`

## Resumen del modulo

`actividadessacd/activ_sacd` gestiona los **sacd encargados** (personas que
atienden) de una actividad. La pantalla permite:

1. Listar actividades en un periodo segun `tipo` (na / sg / sr / sssc / sv /
   sf / sf_na / sf_sg / sf_sr / `falta_sacd` / `solape`) y mostrar, para cada
   actividad, los sacd encargados actuales, junto con la ciudad/centro
   encargado.
2. Asignar un sacd nuevo a una actividad (crea la fila `ActividadCargo` y,
   si es actividad de sv — `id_tipo_activ` empieza por `1` —, tambien crea
   la `Asistencia`).
3. Reordenar el sacd (`+ prioridad` / `- prioridad`) intercambiando
   `id_nom` entre cargos consecutivos, o borrarlo (elimina `ActividadCargo`
   y `Asistencia`).
4. Para `tipo=solape`, lista sacd con actividades incompatibles (el calculo
   vive en `CargoOAsistenteInterface::getSolapes`).

El dispatcher legacy `activ_sacd_ajax.php` mezcla las 5 acciones en un solo
`switch($Qque)`, construye HTML inline y responde con texto plano.

## Estado inicial (antes del refactor)

```
apps/actividadessacd/
├── controller/
│   ├── activ_sacd.php          (105 LOC) pantalla principal (filtros periodo)
│   └── activ_sacd_ajax.php     (738 LOC) dispatcher switch($Qque) con 5 ramas
└── view/
    └── activ_sacd.html.twig    JS + form, renderiza respuesta HTML en #exportar

src/actividadessacd/
├── config/dependencies.php     (solo repos de texto y horario)
├── db/…                        (esquema PG de textos sacd, no tocar)
├── domain/…                    ActividadSacdTexto (sin relacion con activ_sacd)
└── infrastructure/
    ├── persistence/postgresql/ PgActividadSacdTextoRepository
    └── (sin ui/http/controllers)

frontend/actividadessacd/       (no existe)
```

No hay `src/actividadessacd/application/` vinculado al flujo `activ_sacd`,
ni `routes.php`, ni endpoints `/src/actividadessacd/*`.

## Ramas del dispatcher `activ_sacd_ajax.php`

El switch `$Qque` cubre 5 acciones (mas la sub-rama `tipo=solape` dentro
de `lista_activ`, que se trata a parte):

| Rama | Tipo | Salida | Destino refactor |
|------|------|--------|------------------|
| `orden` + `num_orden=mas\|menos` | mutacion | texto plano error / vacio | `sacd_reordenar` |
| `orden` + `num_orden=borrar` | mutacion (cargo + asistencia) | texto plano error / vacio | `sacd_eliminar` |
| `get` | lectura (HTML inline `<td>…</td>`) | HTML | `sacds_encargados_data` (JSON + frontend pinta) |
| `nuevo` | lectura (HTML tabla con sacd del centro + seleccion) | HTML | `sacds_disponibles_data` |
| `asignar` | mutacion (cargo + asistencia si sv) | texto plano error / vacio | `sacd_asignar` |
| `lista_activ` / `solape` | lectura (HTML tabla completa) | HTML | `lista_actividades_sacd_data` / `solapes_sacd_data` |

La rama `lista_activ` internamente se bifurca por `$Qtipo` entre el listado
normal (todas las variantes de tipo) y `solape`. Al partir en dos endpoints
(`lista_actividades_sacd_data` y `solapes_sacd_data`) se eliminan esos
condicionales.

## Consumidores externos

- Menu metamenus (`log/menus/comun.sql`, `proves/aux_metamenus.csv`,
  `documentacion/Documentacion_Obix/menus.csv`): apuntan a
  `apps/actividadessacd/controller/activ_sacd.php`.
- Referencias en docs
  (`documentacion/Documentacion_Obix/8. dre (personas, actividades y encargos).md`,
  `actividadessacd/mapa_activ_sacd.md`).
- No hay consumidores JS/PHP externos del dispatcher `activ_sacd_ajax.php`
  fuera del propio twig.

## Violaciones de `refactor.md`

1. **Dispatcher `$Qque`** con 5 ramas mezclando mutaciones y lecturas.
2. **Mutaciones sin `ContestarJson`**: responden texto plano
   (`echo "hay un error, no se ha guardado"`, o silencio si ok).
3. **Backend `echo` de HTML**: las ramas `get`, `nuevo`, `lista_activ` y
   `solape` construyen HTML en el controller con `<table>`, `<tr>` y
   handlers `onclick`.
4. **Vista en `apps/actividadessacd/view/`** (twig), no en
   `frontend/…/view/`.
5. **Controlador en `apps/`**, no `frontend/actividadessacd/`.
6. **Sin `src/actividadessacd/application/`** para este flujo: la logica
   esta inline en `activ_sacd_ajax.php`.
7. **Funcion suelta `ordena()`** definida en el dispatcher y variable
   global `$txt_where_cargos` compartida.
8. **Typos bug-causantes**: `AsistenteDlRepository->finsById(...)` y
   `AsistenteDlRepository->Guardaar(...)` (ver legacy lineas 146 y 305).
   Se corrigen al moverlo (`findById`, `Guardar`).
9. **Llamada directa a `$oAsisActiv->DBEliminar()`**: se sustituye por
   `$repo->Eliminar($oAsisActiv)` (el contrato del repositorio lo
   proporciona ya).

## Plan de migracion (un solo slice)

1. **`src/actividadessacd/application/`** — use cases:
   - `SacdReordenar` (mutacion, sustituye `ordena()` + `orden mas/menos`).
   - `SacdEliminar` (mutacion, `orden borrar`; elimina cargo + asistencia).
   - `SacdAsignar` (mutacion `asignar`; crea cargo + asistencia si sv).
   - `SacdsEncargadosData` (lectura: sacd encargados de una actividad,
     con `permite_modificar`).
   - `SacdsDisponiblesData` (lectura: listado de sacd posibles).
   - `ListaActividadesSacdData` (lectura: tabla principal).
   - `SolapesSacdData` (lectura: tabla de solapes).
2. **`src/actividadessacd/config/routes.php`** — registra 7 endpoints
   `/src/actividadessacd/<accion>` (GET + POST).
3. **`src/actividadessacd/infrastructure/ui/http/controllers/`** — 7
   controllers finos que hacen `ContestarJson::enviar($err, $data)`.
4. **`frontend/actividadessacd/controller/activ_sacd.php`** — entrada del
   menu; construye las URLs firmadas con `Hash::linkSinVal` y las pasa
   al view.
5. **`frontend/actividadessacd/view/activ_sacd.phtml`** — migrado desde
   twig; JS JSON-aware (`dataType: 'json'`, helpers que construyen las
   tablas desde los arrays devueltos por el backend), sin HTML en el
   backend.
6. **Wrapper legacy** `apps/actividadessacd/controller/activ_sacd.php` →
   `require` al frontend. Comentario `// deprecado: usar frontend/...`.
7. **Borrar** `apps/actividadessacd/controller/activ_sacd_ajax.php` y
   `apps/actividadessacd/view/activ_sacd.html.twig`.
8. **Actualizar menus** `log/menus/comun.sql`, `proves/aux_metamenus.csv`,
   `documentacion/Documentacion_Obix/menus.csv` a
   `frontend/actividadessacd/controller/activ_sacd.php`. Actualizar
   referencias en `documentacion/Documentacion_Obix/*.md`.
9. `php -l` en todos los ficheros nuevos o tocados.

## Estado final

```
src/actividadessacd/
├── application/
│   ├── SacdAsignar.php
│   ├── SacdEliminar.php
│   ├── SacdReordenar.php
│   ├── SacdsEncargadosData.php
│   ├── SacdsDisponiblesData.php
│   ├── ListaActividadesSacdData.php
│   └── SolapesSacdData.php
├── config/
│   ├── dependencies.php
│   └── routes.php
├── db/…                               (sin cambios)
├── domain/…                           (sin cambios)
└── infrastructure/
    ├── persistence/…                  (sin cambios)
    └── ui/http/controllers/
        ├── sacd_asignar.php
        ├── sacd_eliminar.php
        ├── sacd_reordenar.php
        ├── sacds_encargados_data.php
        ├── sacds_disponibles_data.php
        ├── lista_actividades_sacd_data.php
        └── solapes_sacd_data.php

frontend/actividadessacd/
├── controller/
│   └── activ_sacd.php                 (delgado, Hash + ViewNewPhtml)
└── view/
    └── activ_sacd.phtml                (JSON-aware JS, helpers, sin HTML pesado)

apps/actividadessacd/
├── controller/
│   ├── activ_sacd.php                 (wrapper legacy → require frontend)
│   ├── asignar_sacd_auto.php          (wrapper legacy → require frontend, slice 2)
│   ├── com_sacd_activ.php             (wrapper legacy → require frontend, slice 4)
│   ├── com_sacd_activ_periodo.php     (wrapper legacy → require frontend, slice 4)
│   └── com_sacd_txt.php               (wrapper legacy → require frontend, slice 3)
└── model/   (vaciado tras slice 4)
```

## Contrato JSON por endpoint

### `/src/actividadessacd/sacd_asignar`

POST `{id_activ:int, id_nom:int}`. Crea el `ActividadCargo` con el siguiente
`id_cargo` disponible (primero vacio de `CargoRepository::getArrayCargos('sacd')`),
o `max(id_cargo)+1` si todos estan ocupados. Si la actividad es de sv
(`id_tipo_activ[0] === '1'`), tambien crea la fila de `Asistencia`.
Respuesta: `{success, mensaje, data:'ok'}`.

### `/src/actividadessacd/sacd_reordenar`

POST `{id_activ:int, id_nom:int, num_orden:'mas'|'menos'}`. Intercambia
`id_nom` entre el cargo sacd actual y el vecino superior/inferior (ordenado
por `id_cargo`). Respuesta: `{success, mensaje, data:'ok'}`.

### `/src/actividadessacd/sacd_eliminar`

POST `{id_activ:int, id_nom:int, id_cargo:int}`. Elimina el `ActividadCargo`
`{id_activ, id_cargo}` y, si existe, la `Asistencia` `{id_activ, id_nom}`.
Respuesta: `{success, mensaje, data:'ok'}`.

### `/src/actividadessacd/sacds_encargados_data`

POST `{id_activ:int, id_tipo_activ:string, dl_org:string}`. Devuelve los
sacd encargados actuales de la actividad junto con el flag
`permite_modificar`.

```json
{
  "id_activ": 123,
  "permite_ver": true,
  "permite_modificar": true,
  "sacds": [
    {"id_nom": 111, "id_cargo": 2001, "ap_nom": "Doe, John"},
    {"id_nom": 222, "id_cargo": 2002, "ap_nom": "Roe, Jane"}
  ]
}
```

### `/src/actividadessacd/sacds_disponibles_data`

POST `{id_activ:int, seleccion:int}`. `seleccion` es el bitmask
`2=na, 4=paso, 8=sssc, 16=cp`. Devuelve los sacd del centro encargado
(marcados con el `num_orden` del centro) seguidos del listado global filtrado
por `seleccion`.

```json
{
  "id_activ": 123,
  "sacds_ctr": [
    {"id_nom": 55, "ap_nom": "Smith, Bob", "num_orden": 1}
  ],
  "sacds_todos": [
    {"id_nom": 55, "ap_nom": "Smith, Bob"},
    {"id_nom": 66, "ap_nom": "Doe, Jane"}
  ]
}
```

### `/src/actividadessacd/lista_actividades_sacd_data`

POST filtros de periodo + `tipo`. Devuelve la tabla principal como array
(no HTML). Incluye, por actividad, los sacd, el centro encargado y los
flags de permiso (`ver`, `modificar`, `crear`) para que el frontend decida
que renderizar. Para `tipo=falta_sacd` filtra actividades que no tengan
sacd o que, teniendolo, no tengan la fase ok_sacd.

```json
{
  "titulo": "Listado de actividades",
  "tipo": "sg",
  "inicio_iso": "2026-01-01",
  "fin_iso": "2026-12-31",
  "texto_fase_ok_sacd": "Sacd aprobado",
  "mostrar_nota_falta_sacd": false,
  "perm_des": true,
  "filas": [
    {
      "id_activ": 123,
      "nom_activ": "Actividad Foo",
      "f_ini": "01/06/2026",
      "f_fin": "05/06/2026",
      "clase": "plaza4",
      "perm_modificar": true,
      "perm_crear": true,
      "sacds": [
        {"id_nom": 111, "id_cargo": 2002, "ap_nom": "Doe, John"}
      ]
    }
  ]
}
```

### `/src/actividadessacd/solapes_sacd_data`

POST filtros de periodo. Devuelve la tabla de solapes como array.

```json
{
  "titulo": "listado de sacd con actividades incompatibles",
  "texto_fase_ok_sacd": "Sacd aprobado",
  "filas": [
    {
      "id_nom": 55,
      "nom_sacd": "Smith, Bob",
      "actividades": [
        {"clase": "plaza4",         "nom_activ": "Actividad A"},
        {"clase": "plaza4 tachado", "nom_activ": "Actividad A (mismo lugar)"}
      ]
    }
  ]
}
```

## Desviaciones respecto al legacy documentadas

- Las mutaciones legacy responden texto plano (vacio = ok, texto = error).
  Se unifican a JSON `{success, mensaje}`.
- Las ramas `get`, `nuevo`, `lista_activ` y `solape` devolvian HTML inline
  con `<table>`, `<tr>` y onclick handlers. En el refactor, el backend
  devuelve arrays neutros y la tabla se monta en el JS del
  `frontend/actividadessacd/view/activ_sacd.phtml`.
- La rama `lista_activ` con `$Qtipo === 'solape'` se separa en su propio
  endpoint `solapes_sacd_data` para respetar "un endpoint por accion".
- Se corrigen en silencio los typos `finsById` → `findById` y `Guardaar`
  → `Guardar` del dispatcher legacy (eran bugs tapados porque la rama era
  dificil de ejecutar).
- `$oAsisActiv->DBEliminar()` (llamada directa al metodo CRUD de la
  entidad) se sustituye por `$repo->Eliminar($oAsisActiv)` (contrato
  estandar del repositorio).

## Validacion

- `php -l` en los 7 use cases, 7 controllers HTTP, `routes.php`, controller
  frontend y vista.
- Comparar con pantalla legacy: periodo `actual/tot_any`, tipo `sg`, debe
  mostrar el mismo numero de actividades y los mismos sacd encargados.
- Probar mutaciones: asignar, +/- prioridad, borrar.
- `rg "apps/actividadessacd/controller/activ_sacd_ajax.php"` → 0 fuera de
  `languages/*.po*` tras el refactor.

---

## Slice 2: `asignar_sacd_auto`

### Resumen

Pantalla auxiliar del menu "Auto asignar sacd a actividades". Asigna
automaticamente el **sacd titular** del centro encargado a cada actividad
de sr/sg (`id_tipo_activ` regex `.(4|5|7)`) que:

1. Empieza despues de `01-09-<any_final_curs>` (inicio de curso des).
2. Esta marcada como `status = ACTUAL`.
3. Tiene un unico centro encargado con `num_orden = 0` (el principal).
4. Todavia no tiene ningun cargo sacd asignado.

El `observ` del `ActividadCargo` queda a `'auto'` para distinguir las
asignaciones automaticas de las manuales.

### Estado inicial

```
apps/actividadessacd/
├── controller/
│   └── asignar_sacd_auto.php   (57 LOC) 2 pasos: confirmacion + mutacion
└── model/
    └── AsignarSacd.php         (198 LOC) logica de seleccion + asignacion
```

El controlador hace 2 cosas en un mismo endpoint:

- Sin `confirm=yes`: imprime HTML de confirmacion (`<p>` + `<form>`) y
  se autopostea.
- Con `confirm=yes`: ejecuta `AsignarSacd::asignarAuto()` y hace `echo`
  de un texto con el conteo (`"Ya esta. Se ha asignado X. Quedan Y..."`).

### Violaciones de `refactor.md`

1. **Controlador legacy** en `apps/` mezclando UI + mutacion.
2. **Logica de negocio** en `apps/actividadessacd/model/AsignarSacd.php`
   (namespace `actividadessacd\model`), fuera de `src/<modulo>/application/`.
3. **Mutacion sin `ContestarJson`**: responde `echo sprintf(...)` en
   texto plano.
4. **HTML del formulario inline** en el controlador.
5. **Form legacy** con `fnjs_enviar_formulario(this.form)` apuntando a la
   misma URL para bifurcar por POST (`confirm=yes`).

### Plan de migracion (un solo slice)

1. **`src/actividadessacd/application/SacdAsignarAuto.php`** — use case
   tipado que encapsula `selActividades`, `selCtrEncargados`, `selCtrSacd`,
   `ActivSinSacd`, `asignarAuto`. Entrada: `f_ini_iso`. Salida:
   `['asignadas' => int, 'sin_asignar' => int]`. Elimina los metodos mal
   tipados (propiedades publicas sin types, `setF_ini` → parametro de
   `execute`) y el alias conflictivo `AsignarSacd::AsignarSacd()`.
2. **`src/actividadessacd/infrastructure/ui/http/controllers/sacd_asignar_auto.php`**
   — controlador HTTP fino que calcula la fecha de inicio de curso y
   llama al use case, devolviendo JSON `{success, mensaje, data:{asignadas,sin_asignar}}`.
3. **Anadir ruta** en `src/actividadessacd/config/routes.php`:
   `/src/actividadessacd/sacd_asignar_auto` (POST).
4. **`frontend/actividadessacd/controller/asignar_sacd_auto.php`** —
   calcula la fecha de inicio de curso localmente (para mostrarla en la
   pagina), construye URL firmada con `Hash::linkSinVal`, pasa al view.
5. **`frontend/actividadessacd/view/asignar_sacd_auto.phtml`** — pagina
   de confirmacion con boton "continuar" que llama al endpoint por AJAX
   y pinta el mensaje resultado en el propio div.
6. **Wrapper legacy** `apps/actividadessacd/controller/asignar_sacd_auto.php`
   → `require` al frontend.
7. **Borrar** `apps/actividadessacd/model/AsignarSacd.php` (ya no tiene
   callers tras mover la logica).
8. **Actualizar menus** `log/menus/comun.sql`, `proves/aux_metamenus.csv`.
9. `php -l` en todos los ficheros.

### Contrato JSON

#### `/src/actividadessacd/sacd_asignar_auto`

POST `{f_ini_iso: string (YYYY-MM-DD)}`. Asigna sacd titular del centro
encargado a actividades sr/sg de `status=ACTUAL` posteriores a `f_ini_iso`
que no tengan cargo sacd.

```json
{
  "success": true,
  "mensaje": "",
  "data": { "asignadas": 12, "sin_asignar": 3 }
}
```

### Desviaciones respecto al legacy

- La logica dentro del legacy tenia un metodo `AsignarSacd::AsignarSacd(id_activ)`
  (con mayuscula, mismo nombre que la clase) que producia un constructor
  no-standard en PHP. Se renombra a `asignarUna(int $id_activ)` en el
  use case.
- El legacy hacia `echo sprintf(...)` con el conteo. Se pasa a JSON; el
  frontend pinta el mensaje.
- El HTML de confirmacion deja de vivir en el controlador apps; se mueve a
  un `.phtml`.

---

## Slice 3: `com_sacd_txt`

### Resumen

Editor de los textos de comunicacion que se mandan a los sacd. Permite
elegir una `clave` (com_sacd, t_propio, t_f_ini, ...) y un `idioma`
(es, ca, en, ...) y editar el texto asociado. Entrada desde la pagina
`com_sacd_activ_periodo` via `<span class=link onclick=fnjs_update_div>`.

### Estado inicial

```
apps/actividadessacd/
├── controller/
│   ├── com_sacd_txt.php       ( 87 LOC) monta desplegables + hash + texto inicial
│   └── com_sacd_txt_ajax.php  ( 62 LOC) dispatcher switch($Qque) con 2 ramas:
│                               · get_texto  → echo del texto
│                               · update     → upsert o delete si vacio
└── view/
    └── com_sacd_txt.html.twig ( 67 LOC) form con textarea + JS que:
                                · dispara fnjs_get_texto()
                                · fnjs_guardar(form) via jQuery .one('submit')
```

### Violaciones de `refactor.md`

1. **Ajax dispatcher** `com_sacd_txt_ajax.php` en `apps/` con `switch($Qque)`.
2. **Respuestas no JSON**: `get_texto` hace `echo $txt`; `update` no
   responde nada.
3. **Twig** en una pantalla dinamica con estado volatile.
4. **Submit ajax** montado con `$(form).one('submit', ...)` apuntando a
   la misma URL del backend.
5. **HTML del formulario** mezclado con la logica PHP via Twig.

### Plan de migracion

1. **`src/actividadessacd/application/TextoComunicacionData.php`** —
   data builder. Entrada: `{clave, idioma}`. Salida: `{texto: string}`.
2. **`src/actividadessacd/application/TextoComunicacionGuardar.php`** —
   use case mutacion. Entrada: `{clave, idioma, texto}`. Si `texto === ''`,
   elimina la fila; en caso contrario upsert.
3. **Endpoints**:
   - `/src/actividadessacd/texto_comunicacion_data` (POST) → JSON
     `{success, mensaje, data: {texto}}`.
   - `/src/actividadessacd/texto_comunicacion_guardar` (POST) → JSON
     `{success, mensaje, data: 'ok'}`.
4. **`frontend/actividadessacd/controller/com_sacd_txt.php`** — monta
   los `Desplegable` (claves, idiomas), firma las URLs y renderiza el
   view.
5. **`frontend/actividadessacd/view/com_sacd_txt.phtml`** — form con
   textarea + JS que invoca los endpoints via `$.ajax` + `dataType:'json'`
   y parsea la respuesta estandar (`fnjs_parse_rta`).
6. **Wrapper** `apps/actividadessacd/controller/com_sacd_txt.php` →
   `require` al frontend.
7. **Borrar** `apps/actividadessacd/controller/com_sacd_txt_ajax.php` y
   `apps/actividadessacd/view/com_sacd_txt.html.twig`.
8. **Actualizar caller** `apps/actividadessacd/controller/com_sacd_activ_periodo.php`
   para que `$url_com_txt` apunte al frontend (Hash::link sobre la
   nueva ruta). Este fichero sera migrado integramente en el slice 4,
   pero ya necesita la URL nueva.
9. **Documentacion**: `mapa_com_sacd_activ_periodo.md` (el link aun
   aparece ahi), `9. Exterior.md`.

### Contrato JSON

#### `/src/actividadessacd/texto_comunicacion_data`

POST `{clave: string, idioma: string}`. El idioma se normaliza a los 2
primeros caracteres (`ca_ES.UTF-8` → `ca`, igual que el legacy).

```json
{ "success": true, "mensaje": "", "data": { "texto": "..." } }
```

#### `/src/actividadessacd/texto_comunicacion_guardar`

POST `{clave: string, idioma: string, texto: string}`. Si `texto === ''`
y existe la fila, la elimina; si no existe y texto no vacio, crea una
nueva; si existe y texto no vacio, actualiza. Devuelve `data: 'ok'`.

### Desviaciones respecto al legacy

- La rama `get_texto` del dispatcher respondia `echo $txt`; ahora va
  dentro de `data.texto` en el JSON estandar.
- La rama `update` no respondia nada y el legacy `fnjs_guardar`
  mostraba `alert(rta_txt)` solo si habia texto. Ahora ambos endpoints
  responden `{success, mensaje, data}` y el frontend solo avisa ante
  errores.
- El form ya no autopostea con `$(form).one('submit', ...)`; el boton
  "guardar" invoca directamente la funcion JS que hace `$.ajax`.

---

## Slice 4: `com_sacd_activ_periodo` + `com_sacd_activ`

### Resumen

Pantalla unica que lista la atencion de actividades por sacd y permite
enviar por mail el listado al sacd, al ctr del sacd y al jefe de
calendario. Se alimenta de dos flujos de entrada:

1. Menu → `com_sacd_activ_periodo.php` (lista de varios sacds: nagd,
   sssc, y tambien los "sacd de paso" via `PersonaExRepository`).
2. `personas_select` → `com_sacd_activ.php` (un unico sacd, selecccionado
   por checkbox desde la pantalla de personas).

Ambos flujos se unifican en una sola pantalla frontend con el mismo
formulario de filtros y los mismos endpoints.

### Estado inicial

```
apps/actividadessacd/
├── controller/
│   ├── com_sacd_activ_periodo.php (111 LOC) filtro de periodo + botones
│   └── com_sacd_activ.php         (247 LOC) "2-en-1": render listado o
│                                  envio de mails (Qmail=si/no), con
│                                  sub-rama un_sacd / nagd / sssc
├── model/
│   ├── ComunicarActividadesSacd.php (434 LOC) logica de negocio:
│   │                                · getArrayComunicacion()
│   │                                · envairMails() [sic, typo legacy]
│   └── ActividadesSacdFunciones.php (100 LOC) helpers (traducciones,
│                                    lugar de la delegacion)
└── view/
    ├── com_sacd_activ_periodo.html.twig (59 LOC) form filtros + JS
    ├── com_sacd_activ_print.phtml       (174 LOC) tabla print por sacd
    └── com_un_sacd_activ_print.phtml    (17 LOC) wrapper del print
                                         cuando es un_sacd + boton
                                         "enviar mail"/"cancelar"
```

### Violaciones de `refactor.md`

1. **Dos controladores en `apps/`** con logica de UI + negocio mezclada.
2. **Logica de negocio** (434 LOC) en `actividadessacd\model\ComunicarActividadesSacd`,
   fuera de `src/<modulo>/application/`. Incluye un typo (`envairMails`,
   deberia ser `enviarMails`) y HTML inline inmenso (estilos inline en
   cada `<td>`).
3. **Respuestas no JSON**: el controlador `com_sacd_activ.php` hace
   `echo` de HTML directamente (incluso con `exit()` al vuelo ante
   errores de periodo o falta de jefe calendario).
4. **Doble responsabilidad** en `com_sacd_activ.php`: segun `Qmail=si|no`
   renderiza HTML o encola mails en `ColaMail`.
5. **HTML de impresion** (tablas con estilos) generado en backend en
   `com_sacd_activ_print.phtml`.
6. **URL legacy** `apps/actividadessacd/controller/com_sacd_activ.php`
   referenciada desde `frontend/personas/view/personas_select.phtml`.

### Plan de migracion

1. **Mover `ComunicarActividadesSacd`** a
   `src/actividadessacd/application/services/ComunicarActividadesSacdService.php`.
   Corrige typo: `envairMails` → `enviarMails`. El HTML inline del mail
   se mantiene intacto (es el formato del mail, no UI).
2. **Mover `ActividadesSacdFunciones`** a
   `src/actividadessacd/application/services/ActividadesSacdHelper.php`.
3. **`src/actividadessacd/application/ComunicacionActividadesSacdData.php`**
   — data builder. Construye la pagina entera: resuelve `que` (nagd /
   sssc / un_sacd) aplicando regla `rol === 'p-sacd'`, calcula periodo,
   llama al service y devuelve:
   ```
   {
     que, propuesta, mi_dele, lugar_fecha, periodo_txt,
     sacds: [...], sacds_paso: [...]
   }
   ```
   donde cada entrada es `{id_nom, nom_ap, txt: {clave: texto}, actividades: [...]}`.
4. **`src/actividadessacd/application/ComunicacionActividadesSacdEnviar.php`**
   — mutacion. Reutiliza la data + `service->enviarMails()`. Devuelve
   texto de error vacio en caso de exito, o descriptivo si falla
   (jefe calendario no definido, sin mail del jefe, etc.) en lugar de
   `exit(...)` como el legacy.
5. **Endpoints**:
   - `/src/actividadessacd/comunicacion_activ_sacd_data` (POST) →
     JSON `{success, mensaje, data:{...}}`.
   - `/src/actividadessacd/comunicacion_activ_sacd_enviar` (POST) →
     JSON `{success, mensaje, data:'ok'}`.
6. **`frontend/actividadessacd/controller/com_sacd_activ_periodo.php`**
   — pantalla unica con el form de filtros (PeriodoQue + selectores
   ocultos de `que`, `id_nom`, `propuesta`). Detecta el flujo
   `un_sacd` (ya sea porque `Qque === 'un_sacd'` o porque viene un
   `sel[]` del personas_select) y **auto-dispara** `fnjs_ver()` al
   cargar la pagina.
7. **`frontend/actividadessacd/view/com_sacd_activ_periodo.phtml`** —
   form + `<div id="exportar">` + JS que:
   - `fnjs_ver()` → POST data → reconstruye HTML print cliente.
   - `fnjs_enviar_mails()` → POST enviar → alert ok/error.
   - `fnjs_construir_listado(data)` → pinta las tablas print-friendly
     (cabecera por sacd, tabla por sacd con las actividades, y a
     continuacion los "sacd de paso" si aplica).
8. **Wrappers** para las dos URLs legacy:
   - `apps/actividadessacd/controller/com_sacd_activ_periodo.php` →
     `require` al frontend.
   - `apps/actividadessacd/controller/com_sacd_activ.php` → tambien
     `require` al mismo frontend controller.
9. **Actualizar** `frontend/personas/view/personas_select.phtml` para
   apuntar al nuevo path.
10. **Borrar** los 2 twig/phtml legacy y los 2 modelos movidos.
11. **Actualizar menus** `log/menus/comun.sql`, `proves/aux_metamenus.csv`,
    `documentacion/Documentacion_Obix/menus.csv`.
12. **Docs** `mapa_com_sacd_activ_periodo.md`, `9. Exterior.md`,
    `8. dre`, `2. Gestion`, `10. Supernumerarios`.

### Contrato JSON

#### `/src/actividadessacd/comunicacion_activ_sacd_data`

POST `{que, id_nom, propuesta, periodo, year, empiezamin, empiezamax, sel[]}`.
Campos del form de filtros. Devuelve:

```json
{
  "success": true,
  "mensaje": "",
  "data": {
    "que": "nagd",
    "propuesta": "",
    "mi_dele": "xy",
    "lugar_fecha": "ciudad, 23.04.26",
    "periodo_txt": "curso 2025-26",
    "sacds": [
      {
        "id_nom": 123,
        "nom_ap": "Perez Lopez, Juan",
        "txt": { "com_sacd": "...", "t_propio": "...", ... },
        "actividades": [
          { "propio": "t", "f_ini": "23-IV-26", ..., "observ": "..." }
        ]
      }
    ],
    "sacds_paso": [ ... ]
  }
}
```

#### `/src/actividadessacd/comunicacion_activ_sacd_enviar`

POST con los mismos campos. Reconstruye internamente el listado y
encola los mails en `ColaMail`. Responde `{success, mensaje, data:'ok'}`.

### Desviaciones respecto al legacy

- El typo del metodo `envairMails()` → `enviarMails()`.
- El legacy hacia `exit(...)` en varios sitios (periodo invalido, jefe
  calendario no definido, sin mail). Ahora esos casos devuelven mensaje
  en el JSON (`mensaje`) y el cliente lo muestra.
- El legacy mezclaba listar + enviar en el mismo controller
  (`com_sacd_activ.php`) con la flag `Qmail`; aqui se parten en dos
  endpoints distintos.
- Los dos controladores legacy (`com_sacd_activ.php` y `com_sacd_activ_periodo.php`)
  quedan como wrappers; la UI real es una sola pagina `com_sacd_activ_periodo`
  en el frontend, que detecta y auto-carga el flujo `un_sacd`.
- El HTML de impresion se reconstruye en cliente a partir del JSON; los
  estilos `print` viven en el `<style>` del phtml.

---

## Cierre DI (2026-06-06)

Los 14 controllers en `infrastructure/ui/http/controllers/` usan
`DependencyResolver::get()` (sin `::execute()` estático ni `new` de use
cases). Entrada POST via `input_string` / `input_int` / `input_string_list`.

### Resultado del cierre DI

| Criterio | Estado |
|----------|--------|
| `$GLOBALS['container']` en `src/actividadessacd/` | **0** |
| Controllers HTTP con `DependencyResolver::get()` | **14/14** |
| `application/` con constructor DI | **16** clases (14 use cases + 2 services) |
| Pg repos con `GlobalPdo::get()` | **1/1** (`PgActividadSacdTextoRepository`) |
| Casos de uso en `config/dependencies.php` | **17** entradas `autowire()` (1 repo + 2 services + 14 use cases) |
| Tests `tests/unit/actividadessacd/` | **74 OK** |
| Tests `tests/integration/actividadessacd/` | **28 OK** |

### `src/actividadessacd/config/dependencies.php`

Registra el repositorio del módulo (`ActividadSacdTextoRepositoryInterface`),
`ActividadesSacdHelper`, `ComunicarActividadesSacdService` (con deps
externas de actividades, cargos, centro, personas, ubis, usuarios, etc.)
y todos los use cases: `ComSacdActivPeriodoPageData`,
`ComunicacionActividadesSacdData`, `ComunicacionActividadesSacdEnviar`,
`ListaActividadesSacdData`, `LocalesDesplegableData`, `SacdAsignar`,
`SacdAsignarAuto`, `SacdEliminar`, `SacdReordenar`, `SacdsDisponiblesData`,
`SacdsEncargadosData`, `SolapesSacdData`, `TextoComunicacionData`,
`TextoComunicacionGuardar`.

### PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (inicio) | `composer phpstan:file -- src/actividadessacd/` | **155** |
| 2026-06-06 (cierre) | `composer phpstan:file -- src/actividadessacd/` | **0** |

Áreas abordadas en el cierre (155 → 0):

- Application: constructor DI en los 14 use cases y 2 services; helpers
  `input_*`; tipos de retorno en payloads JSON; `PermisosActividades`
  instanceof guards; `clone` de services configurables en Data/Enviar.
- Domain: entidad `ActividadSacdTexto`, VOs y contrato con PHPDoc
  acotado; setters nullable en VOs.
- Repo `PgActividadSacdTextoRepository`: guards PDO, `GlobalPdo::get()`,
  tipos de retorno `list<>`.
- HTTP controllers: `DependencyResolver::get()` + helpers `input_*`.
- Cross-module: PHPDoc en `CargoOAsistenteInterface::getSolapes()` para
  aceptar `PersonaSacd`; corrección iteración `getArrayIdsWithKeyFini`
  en `SacdAsignarAuto`.

### Deuda post-refactor

#### Completado

- [x] 0 `$GLOBALS['container']` en todo `src/actividadessacd/`
- [x] Todos los controllers HTTP via `DependencyResolver`
- [x] Casos de uso con constructor DI
- [x] `dependencies.php` con todos los use cases
- [x] Tests `tests/unit/actividadessacd/`: 74 tests
- [x] Tests `tests/integration/actividadessacd/`: 28 tests
- [x] PHPStan `src/actividadessacd/` en 0 (phpstan-nobaseline.neon)

#### Pendiente

- [ ] 2 controladores frontend con comentario «Sin `use src\`» (ver
  [`frontend_pendiente_refactor_src.md`](frontend_pendiente_refactor_src.md)):
  `frontend/actividadessacd/controller/activ_sacd.php`,
  `frontend/actividadessacd/controller/asignar_sacd_auto.php`

### Checklist de cierre

Ver [`REFACTOR_INDICE.md`](REFACTOR_INDICE.md#checklist-cerrar-un-módulo).

- [x] `$GLOBALS['container']` migrado a DI por constructor en `application/`
- [x] Controllers HTTP sin `$GLOBALS` directo (`DependencyResolver`)
- [x] `dependencies.php` con todos los use cases
- [x] Tests existentes pasan (`tests/unit/actividadessacd/`: 74;
  `tests/integration/actividadessacd/`: 28)
- [x] PHPStan `src/actividadessacd/` en 0 (phpstan-nobaseline.neon)
