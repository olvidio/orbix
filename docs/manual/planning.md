---
tipo: "manual_usuario"
modulo: "planning"
flujos: 7
estado_revision: "revisado"
---

# Manual De Usuario - planning

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Elegir las casas y el periodo del planning

### Para Que Sirve

Elegir de qué casas se quiere ver el calendario de ocupación y en qué periodo.

### Donde Entrar

- Planning por casas (filtros) (frontend/planning/controller/planning_casa_que.php)
- **Legacy (actual):** `dre/Calendario/… > planning > por casas` · `adl > Gestión casas > Planing Casas`
- **Pills2 (actual):** `ACTIVIDADES > Herramientas de calendario > Planning calendario actual`
- **Legacy (propuesta):** `adl|Calendario|dre > Nuevo Calendario > nuevo planing`
- **Pills2 (propuesta):** `ACTIVIDADES > Herramientas de calendario > Planning calendario en estudio`

### Tareas Habituales

#### Elegir las casas y el periodo del planning

1. Abrir «BÚSQUEDA DE CASAS CUYO PLANNING INTERESA».
2. Elegir el grupo de casas: **casas sólo sv**, **casas sólo sf**, **casas comunes**, **casas sv**, **casas sf**, **casas y ctr sf**, **una casa o lugar** (y elegirla en el desplegable), **todas las actividades sv** o **todas las actividades sf**.
3. Indicar si se quieren incluir las **casas sin actividad** (no / sí).
4. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
5. Pulsar **Planning vista tabla** (calendario en pantalla) o **vista para imprimir**.

### Errores O Avisos Frecuentes

- `No se encuentra el usuario`

### Permisos

- La lógica de alcance está en el caso de uso: rol `PAU_CDC`, `have_perm_oficina('des'|'vcsd')` y

### Referencias Internas

- Flujo: `planning.planning_casa_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_casa_que.md`

## Ver el calendario de casas

### Para Que Sirve

Ver el calendario de ocupación de las casas elegidas: qué actividad hay en cada casa y cada día.

### Donde Entrar

- Planning por casas (filtros) (frontend/planning/controller/planning_casa_que.php)
- Selección de casas (planning) (frontend/planning/controller/planning_casa_select.php)
- sin entrada de menú en el índice (subflujo del planning por casas)

### Tareas Habituales

#### Ver el calendario de casas

1. Aparece «Calendario de casas» con los meses del periodo en columnas y una fila por casa; los domingos van marcados.
2. Cada actividad es una barra con su tipo abreviado (por ejemplo «sg crt», «n ca»); el color indica el tipo y si está en proyecto o provisional. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
3. **Descargar html** guarda el calendario como página web.
4. Desde la entrada de menú de **nuevo planning** del calendario de propuestas, la tabla permite crear una actividad nueva en una casa o modificar una existente.

### Errores O Avisos Frecuentes

- `Faltan fechas de periodo (f_ini_iso / f_fin_iso).`

### Permisos

- Sin control propio en el caso de uso; autorización en frontend + menú (`$_SESSION['oPerm']`).

### Referencias Internas

- Flujo: `planning.planning_casa_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_casa_ver.md`

## Ver el planning de las personas de un centro

### Para Que Sirve

Ver el calendario de actividades de las personas de un centro (o de todos los centros de un colectivo).

### Donde Entrar

- Planning por centro (filtros) (frontend/planning/controller/planning_ctr_que.php)
- **Legacy:** `dre/Calendario/… > planning > por centro` · `vsr/scdl > planning > por ctr`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > Planning por ctr`

### Tareas Habituales

#### Ver el planning de las personas de un centro

1. Abrir «ACTIVIDADES DE LAS PERSONAS DE UN CENTRO».
2. Escribir el **Centro** (si se deja vacío salen todos los n ordenados por centro) o marcar **todos los ctr con n**, **todos los ctr con agd** o **todos los ctr con s**.
3. Elegir **incluir sacd**: no / sí.
4. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
5. Pulsar **Planning vista tabla** o **vista para imprimir**.
6. Si un centro no existe o no tiene personas, avisa «No encuentro este ctr» o «No encuentro personas para …». El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.

### Errores O Avisos Frecuentes

- `Faltan fechas de periodo`
- `No encuentro este ctr`
- `No encuentro personas para %s`

### Permisos

- Sin control propio; autorización en frontend + menú.

### Referencias Internas

- Flujo: `planning.planning_ctr_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_ctr_select.md`

## Buscar personas y ver su planning

### Para Que Sirve

Buscar personas de la dl y ver su calendario de actividades.

### Donde Entrar

- Planning por persona (filtros) (frontend/planning/controller/planning_persona_que.php)
- | Params | Legacy | Pills2 |
- |--------|--------|--------|
- | `obj_pau=PersonaDl` | `… > planning > persona r/dl` · `scdl > persona dl` | `ACTIVIDADES > … > Plannig por personas` |
- | `PersonaEx&na=a|n` | `scdl > planning > agd/num de paso` | igual |
- | `PersonaSacd` | `dre > planning > sacd r/dl` | — |
- | `PersonaSacd&es_sacd=1` | — | `ACTIVIDADES > … > Plannig por personas sacd` |

### Tareas Habituales

#### Buscar personas y ver su planning

1. Abrir «BÚSQUEDA DE PERSONAS DE LA DL» y rellenar, si se quiere, **Nombre**, **Primer apellido**, **Segundo apellido** o **Centro**.
2. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
3. Pulsar **Buscar**: sale «N personas encontradas» con tipo, nombre y apellidos y centro.
4. Marcar a una o varias personas (o **marcar todos** / **desmarcar todos**) y pulsar **vista tabla** para el calendario, **vista para imprimir** o **ver actividades**.
5. Pulsar el nombre de una persona abre su ficha.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; `obj_pau` y alcance vienen del menú/frontend.

### Referencias Internas

- Flujo: `planning.planning_persona_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_persona_select.md`

## Ver el calendario de una persona

### Para Que Sirve

Ver el calendario de actividades de las personas elegidas.

### Donde Entrar

- Planning por persona (filtros) (frontend/planning/controller/planning_persona_que.php)
- Listado de personas (planning) (frontend/planning/controller/planning_persona_select.php)
- sin entrada de menú en el índice (subflujo del planning por persona)

### Tareas Habituales

#### Ver el calendario de una persona

1. Aparece el calendario con los meses del periodo (enero - 2027, febrero - 2027…) y una fila por persona; cada actividad es una barra con su tipo. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
2. **Descargar html** guarda el calendario como página web.

### Errores O Avisos Frecuentes

- `Faltan fechas de periodo`

### Permisos

- Sin control propio; autorización en frontend + menú.

### Referencias Internas

- Flujo: `planning.planning_persona_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_persona_ver.md`

## Elegir la zona y el periodo

### Para Que Sirve

Elegir la zona de sacerdotes y el trimestre o mes del planning.

### Donde Entrar

- Planning por zonas SACD (filtros) (frontend/planning/controller/planning_zones_que.php)
- **Legacy:** `dre > planning > por zonas` · `vsr > planning > por zonas` · `exterior > sacd > Misas > Planning zonas`
- **Pills2:** `ACTIVIDADES > Herramientas de calendario > por zonas`
- Propuesta: `dre > propuestas > planing zonas`

### Tareas Habituales

#### Elegir la zona y el periodo

1. Abrir «trimestre o mes que interesa» y marcar un trimestre (enero-marzo, abril-junio, julio-septiembre, octubre-diciembre) o un mes concreto.
2. Elegir el **año** y la **zona** (según tu rol, solo las tuyas o también «todo»).
3. Marcar si se quieren ver las actividades (sí / no).
4. Pulsar **planning vista tabla**.

### Errores O Avisos Frecuentes

- `No se encuentra el usuario`
- `No tiene permiso para ver esta página`

### Permisos

- Rol `p-sacd`: requiere `is_jefeCalendario()` o `id_nom` jefe con zonas asignadas.
- Resto de roles: zonas según `getArrayZonas(null)`.

### Referencias Internas

- Flujo: `planning.planning_zones_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_zones_que.md`

## Ver el planning de una zona

### Para Que Sirve

Ver el calendario de los sacerdotes de una zona.

### Donde Entrar

- Planning por zonas SACD (filtros) (frontend/planning/controller/planning_zones_que.php)
- sin entrada de menú en el índice (subflujo del planning por zonas)

### Tareas Habituales

#### Ver el planning de una zona

1. Aparece el calendario con el nombre de la zona, los meses elegidos en columnas y una fila por sacerdote. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
2. **Descargar html** guarda el calendario como página web.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio en el caso de uso; alcance de zonas ya filtrado en `planning_zones_que`.

### Referencias Internas

- Flujo: `planning.planning_zones_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/planning/flujos/planning_zones_select.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/planning/flujos/`.
