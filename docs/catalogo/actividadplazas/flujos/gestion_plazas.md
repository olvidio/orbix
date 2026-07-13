---
id: "actividadplazas.gestion_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Gestion Plazas"
capacidad: "actividadplazas.gestion_plazas.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.gestion_plazas", "actividadplazas.pantalla.plazas_balance_dl"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Gestion Plazas

Distribuir plazas entre las delegaciones del grupo de estudios: consultar el cuadro de plazas del
periodo y editar totales, concedidas o pedidas por celda.

## Objetivo De Usuario

Ver, para un periodo y tipo de actividad, cuántas plazas tiene cada actividad y cómo se reparten
(concedidas/pedidas) entre las delegaciones del grupo, y ajustar esos valores desde la propia tabla.

## Punto De Entrada

Menú de plazas → **Gestión de plazas** (ver "Ruta de menú"). Se abre la pantalla
`actividadplazas.pantalla.gestion_plazas`.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.plazas_balance_dl`

## Escenarios Inferidos

### Obtener Datos

1. Elegir el periodo (año + periodo, o rango de fechas) y pulsar **Buscar**.
2. El sistema carga el cuadro desde `gestion_plazas_data` (actividades × delegaciones).

Endpoints asociados:
- `/src/actividadplazas/gestion_plazas_data`

### Crear Actualizar

1. Localizar la actividad en la tabla.
2. Doble clic en una celda editable (total, concedidas `-c` o pedidas `-p` de mi delegación).
3. Escribir el nuevo valor; se guarda al instante vía `gestion_plazas_update`.
4. Si la actividad no tiene plazas en el calendario común, se muestra el aviso para darlas de alta antes.

Endpoints asociados:
- `/src/actividadplazas/gestion_plazas_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.colName`
- `form.data`
- `html.refresh`
- `post.dl`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_tipo_activ`
- `post.periodo`
- `post.refresh`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Errores Conocidos

- `no se encuentra la actividad`
- `hay un error, no se ha guardado`
- Aviso de calendario (la actividad aún no tiene plazas en el calendario común).

## Ruta de menú

- **Legacy:** vsm > ca > Gestión de plazas (y variantes por perfil/tipo: dagd, vsg, vest…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Distribución plazas ca n entre r/dl (y variantes por tipo/colectivo)
