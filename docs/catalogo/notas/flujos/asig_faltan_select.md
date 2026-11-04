---
id: "notas.asig_faltan_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Buscar alumnos por número de asignaturas pendientes"
capacidad: "notas.asig_faltan_select.gestionar"
pantallas_principales: ["notas.pantalla.asig_faltan_select"]
fragmentos: ["notas.pantalla.asig_faltan_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asig_faltan_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asig Faltan Select

Propuesta generada automaticamente desde la capacidad `notas.asig_faltan_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver los alumnos a los que les faltan como mucho N asignaturas.

## Punto De Entrada

Menú **vest > actas... > buscar asig. pendientes** (Pills2: ESTUDIOS > Preparación planes estudio > Filtro alumno/asignatura).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.asig_faltan_select`

## Escenarios Inferidos

### Buscar alumnos por número de asignaturas pendientes

Pasos propuestos:
1. Entrar en **buscar asig. pendientes**.
2. En **Búsqueda de personas por número de asignaturas** marcar **numerarios** y/o **agregados** y elegir el ciclo con el botón **bienio** o **cuadrienio** (en cuadrienio, además, **año I** y/o **año II-IV**).
3. Escribir el **número de asignaturas que faltan** (no se incluyen las cursadas sin examinar). Marcar **incluir lista de asignaturas** si se quiere ver cuáles son.
4. Pulsar **Buscar** (o **Borrar** para limpiar). El resultado se titula, por ejemplo, «lista de numerarios a los que faltan 2 o menos asignaturas para finalizar el cuadrienio»; si no hay nadie, «no hay ninguna fila».

Endpoints asociados:
- `/src/notas/asig_faltan_select_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.b_c`
- `post.c1`
- `post.c2`
- `post.lista`
- `post.numero`
- `post.personas_agd`
- `post.personas_n`
- `post.stack`

Acciones JavaScript:
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_tesera`

## Endpoints Del Flujo

- `/src/notas/asig_faltan_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
