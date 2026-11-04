---
id: "actividadplazas.gestion_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Ver el reparto de plazas de un periodo"
capacidad: "actividadplazas.gestion_plazas.gestionar"
pantallas_principales: ["actividadplazas.pantalla.gestion_plazas"]
fragmentos: ["actividadplazas.pantalla.gestion_plazas", "actividadplazas.pantalla.plazas_balance_dl"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Gestion Plazas

Distribuir plazas entre las delegaciones del grupo de estudios: consultar el cuadro de plazas del
periodo y editar totales, concedidas o pedidas por celda.

## Objetivo De Usuario

Ver, para un periodo y un tipo de actividad, cuántas plazas tiene cada actividad y cómo se reparten (concedidas y pedidas) entre las delegaciones del grupo, y ajustar esos números desde la propia tabla.

## Punto De Entrada

Menú de plazas > **Gestión de plazas**.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.plazas_balance_dl`

## Escenarios Inferidos

### Ver el reparto de plazas de un periodo

Pasos propuestos:
1. Abrir **Gestión de plazas**.
2. Elegir el periodo (año y periodo, o un rango de fechas) y pulsar **Buscar**.
3. Aparece un cuadro con las actividades en filas y las delegaciones en columnas.

Endpoints asociados:
- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

### Cambiar las plazas de una actividad

Pasos propuestos:
1. Localizar la actividad en la tabla.
2. Hacer doble clic en una celda editable: el total, las concedidas (**-c**) o las pedidas (**-p**) de la propia delegación.
3. Escribir el nuevo valor; se guarda al momento.
4. Si la actividad no tiene plazas en el calendario común, sale un aviso: hay que darlas de alta antes.

Endpoints asociados:
- `/src/actividadplazas/gestion_plazas_data`
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
