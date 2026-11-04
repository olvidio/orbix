---
id: "notas.asig_faltan_personas_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Buscar los alumnos que deben una asignatura"
capacidad: "notas.asig_faltan_personas_select.gestionar"
pantallas_principales: ["notas.pantalla.asig_faltan_personas_select"]
fragmentos: ["notas.pantalla.asig_faltan_personas_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asig_faltan_personas_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asig Faltan Personas Select

Propuesta generada automaticamente desde la capacidad `notas.asig_faltan_personas_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver qué alumnos deben una asignatura concreta.

## Punto De Entrada

Menú **vest > actas... > buscar asig. pendientes** (Pills2: ESTUDIOS > Preparación planes estudio > Filtro alumno/asignatura).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.asig_faltan_personas_select`

## Escenarios Inferidos

### Buscar los alumnos que deben una asignatura

Pasos propuestos:
1. Entrar en **buscar asig. pendientes**.
2. En **Búsqueda de personas por asignatura** marcar **numerarios** o **agregados**, el ciclo (**bienio**, **cuadrienio**, **año I**, **año II-IV**) y elegir la **Asignatura**.
3. Pulsar **Buscar** (o **Borrar** para limpiar).
4. Se muestra la lista de alumnos que la deben.

Endpoints asociados:
- `/src/notas/asig_faltan_personas_select_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.b_c`
- `post.c1`
- `post.c2`
- `post.id_asignatura`
- `post.personas_agd`
- `post.personas_n`
- `post.stack`

Acciones JavaScript:
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_tesera`

## Endpoints Del Flujo

- `/src/notas/asig_faltan_personas_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
