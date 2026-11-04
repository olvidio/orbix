---
id: "misas.cuadricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Editar una celda del plan de misas"
capacidad: "misas.cuadricula.gestionar"
pantallas_principales: ["modificar_plan_de_misas", "preparar_plan_de_misas", "modificar_plantilla"]
fragmentos: ["ver_cuadricula_zona", "modificar_cuadricula_zona", "crear_nuevo_periodo", "importar_plantilla"]
acciones: ["editar_celda", "preparar_periodo", "importar_plantilla"]
endpoints:
  - "/src/misas/ver_cuadricula_zona_data"
  - "/src/misas/cuadricula_update"
  - "/src/misas/desplegable_sacd"
  - "/src/misas/crear_nuevo_periodo_data"
  - "/src/misas/importar_plantilla_data"
estado_revision: "revisado"
---

# Flujo - Edición de cuadrícula (plan / preparar / plantilla)

## Objetivo De Usuario

Asignar, cambiar o borrar el sacerdote de una celda del plan de misas; preparar un periodo nuevo o copiar una plantilla a otra.

## Punto De Entrada

Menú **dre > Misas** (Pills2: ATENCIÓN SACD > Gestión de misas).

## 1. Guardar celda → `POST /src/misas/cuadricula_update`

Al hacer Save en el modal (`commitCurrentEdit` en `modificar_cuadricula_zona.phtml`), el front envía `application/x-www-form-urlencoded`:

| Campo POST | Ejemplo | Significado |
|------------|---------|-------------|
| `uuid_item` | `a1b2c3d4-…` | UUID del `EncargoDia`. Obligatorio. Si la celda estaba vacía, el JS genera uno (blob URL) para crear. |
| `key` | `JP#1234` | Sacerdote: `iniciales#id_nom`. Vacío → **borra** la asignación. |
| `id_enc` | `42` | Id del encargo (fila de misa). |
| `dia` | `2026-08-10` | Día ISO (`Y-m-d`) de la celda (`meta.dia`). |
| `tstart` | `08:00` | Hora inicio (texto del clockpicker; puede ir vacío). |
| `tend` | `08:30` | Hora fin. |
| `observ` | `con organista` | Observaciones; en UI se muestra `*` junto a iniciales si no vacío. |
| `status` | `1` | Estado de la celda en el plan (`p`). Vacío → propuesta. En plantilla viaja pero no se persiste. |
| `tipo_plantilla` | `p` | Contexto de vista: `p` = plan; `s1`/`s3`/`d1`/`d3`/`m1`/`m3` = plantillas. Afecta colores de estado. |
| `id_zona` | `3` | Zona activa. |

HashF firma: `dia!id_enc!key!observ!status!tend!tstart!uuid_item!tipo_plantilla!id_zona`.

**Nota `key`:** el desplegable y el POST usan `iniciales#id_nom`. En `meta` de `ver_cuadricula_zona_data` a veces viene `id_nom#iniciales`; el JS tolera ambas con `resolveSacdSelectedKey` / `sacdKeyToIdNom`. El backend (`CuadriculaUpdate`) toma `id_nom` de la **segunda** parte tras `#` → el POST debe ser `iniciales#id_nom`.

Respuesta `data.meta` (colores/textos para refrescar grid): `color_misa`, `id_sacd_anterior`, `texto_anterior`, `color_fondo_anterior`, `texto`, `color_fondo`, `texto_sacd`, `comprobacion`, …

Ficha: [`api/cuadricula_update.md`](../api/cuadricula_update.md).

---

## 2. Elegir sacerdote → `POST /src/misas/desplegable_sacd`

Sí: desplegable de sacerdotes (valor = `iniciales#id_nom`, etiqueta = nombre con iniciales).

Radios del modal (flags bitmask `seleccion`):

| Radio | valor | Filtro |
|-------|-------|--------|
| libre a 1ª hora | `1` | Sacds de zona libres a 1ª hora ese día |
| zona | `2` | Todos los sacds de la zona (default si no hay selección) |
| dl | `4` | Sacds dl (`situacion=A`, tablas n/a) |
| de paso | `8` | Sacds activos (amplio) |

POST: `id_zona`, `id_sacd` (id_nom actual o 0), `seleccion`, `dia` (ISO).

Ficha: [`api/desplegable_sacd.md`](../api/desplegable_sacd.md).

---

## 3. Preparar periodo → `POST /src/misas/crear_nuevo_periodo_data`

Botón **preparar** en `preparar_plan_de_misas.phtml` → `crear_nuevo_periodo.php` (mapea `tipoplantilla` → `tipo_plantilla`) → API. Borra asignaciones del periodo salvo celdas con estado **visible ctr** (se conservan sin sobrescribir desde plantilla).

| Campo | Obligatorio | Ejemplo / valores |
|-------|-------------|-------------------|
| `id_zona` | sí | `3` |
| `tipo_plantilla` | sí | `s1`, `s3`, `d1`, `d3`, `m1`, `m3` (plantilla origen; el plan creado es real) |
| `periodo` | sí | `proxima_semana` \| `proximo_mes` \| `otro` |
| `empiezamin` | si `otro` | `01/08/2026` (dd/mm/yyyy en front) |
| `empiezamax` | si `otro` | `31/08/2026` |
| `orden` | no | default `desc_enc` |
| `seleccion` | no | filtro sacd (entero; a menudo 0) |

Efecto: borra `EncargoDia` del periodo en la zona y los recrea desde la plantilla; responde payload de cuadrícula (mismo shape que `ver_cuadricula_zona_data`). Luego el front recarga la cuadrícula en modo plan (`tipo_plantilla=p`).

Ficha: [`api/crear_nuevo_periodo_data.md`](../api/crear_nuevo_periodo_data.md).

---

## 4. Importar plantilla → `POST /src/misas/importar_plantilla_data`

Botón **importar** en `modificar_plantilla.phtml`:

| Campo | Origen UI | Ejemplo |
|-------|-----------|---------|
| `id_zona` | `#id_zona` | `3` |
| `tipo_plantilla_origen` | `#importar_de_plantilla` | `s1` |
| `tipo_plantilla_destino` | `#tipo_plantilla` (la que se edita) | `d1` |

Copia asignaciones origen → destino (borra destino en su rango de fechas ficticias de plantilla). El JS deshabilita orígenes con la misma letra inicial (`s`/`d`/`m`) que el destino.

Ficha: [`api/importar_plantilla_data.md`](../api/importar_plantilla_data.md).

---

## 5. Datos de celda en `ver_cuadricula_zona_data` (para editar en Android)

`data.data_cuadricula[]`: cada fila tiene:

- `encargo` — texto visible (descripción encargo o nombre sacd / título).
- `id_nom` — en filas sacd; vacío en filas misa.
- `color_encargo` — CSS class de la columna encargo (`azulclaro`/`violetaclaro`/`amarilloclaro`/`titulo`).
- Campos día `YYYY-MM-DD` — valor mostrado (p. ej. `JP 08:00*`).
- `meta` — mapa por campo día.

### `meta[YYYY-MM-DD]` en celdas editables (`tipo: "misas"`)

| Clave | Uso al editar |
|-------|----------------|
| `tipo` | `"misas"` → editable; `"sacd"` / `"titulo"` → no abrir modal |
| `uuid_item` | Id EncargoDia; vacío = celda libre (generar UUID al crear) |
| `key` | Sacd asignado (`id_nom#iniciales` en carga; normalizar a `iniciales#id_nom` al POST) |
| `id_enc` | Encargo de la fila |
| `dia` | ISO del día (puede diferir del field en plantillas ×3) |
| `tstart` / `tend` | Horas actuales |
| `observ` | Observaciones |
| `color` / `texto` | UI (estado / avisos); no se reenvían en update |

Columnas: `columns_cuadricula` (JSON string o array) con `id`/`name`/`field` = día ISO o `encargo`.

Ficha: [`api/ver_cuadricula_zona_data.md`](../api/ver_cuadricula_zona_data.md).

## Valores `tipo_plantilla` (`PlantillaConfig`)

| Código | Significado |
|--------|-------------|
| `p` | Plan de misas (calendario real) |
| `s1` / `s3` | Plantilla semanal 1 / 3 |
| `d1` / `d3` | Plantilla domingos 1 / 3 |
| `m1` / `m3` | Plantilla mensual 1 / 3 |

## Escenarios Inferidos

### Editar una celda del plan de misas

Pasos propuestos:
1. Entrar en **Modificar plan** y buscar la zona y el periodo.
2. Pulsar la celda del encargo y día; elegir el sacerdote (o dejarla vacía para borrar), con horario y observaciones si procede.
3. Para periodos nuevos usar **Nuevo plan**; para copiar plantillas, **Modificar plantilla > importar**.
4. Leyenda de la fila de cada sacerdote (por día): **SI** = el sacerdote está en la zona ese día; **NO** = no está en la zona ese día; **--** = ese día está en otro sitio (al pasar el ratón sale «Está en …»). El fondo rojo avisa de un problema: «Tiene dos Misas a primera hora» o «No está en la zona y tiene Misa a primera hora».

Endpoints asociados:
- `/src/misas/ver_cuadricula_zona_data`
- `/src/misas/cuadricula_update`
- `/src/misas/desplegable_sacd`
- `/src/misas/crear_nuevo_periodo_data`
- `/src/misas/importar_plantilla_data`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plan / Preparar / Modificar plantilla
- **Pills2:** ATENCIÓN SACD > Gestión de misas > …
