---
id: "actividadestudios.ca_posibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Ver los posibles asistentes al ca"
capacidad: "actividadestudios.ca_posibles.gestionar"
pantallas_principales: ["actividadestudios.pantalla.ca_posibles"]
fragmentos: ["actividadestudios.pantalla.ca_posibles"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/ca_posibles_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ca Posibles

Cálculo y listado de créditos/asignaturas cursables por alumno en cada CA.

## Objetivo De Usuario

Obtener, para el centro y el periodo elegidos, el cuadro de posibles asistentes al ca: por cada alumno, los créditos que puede cursar, las asignaturas pendientes y enlaces al detalle.

## Punto De Entrada

Se llega desde el formulario de búsqueda de posibles ca (menú de estudios) o desde la búsqueda de personas, con la opción de posibles ca.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.ca_posibles`
- `actividadestudios.pantalla.ca_posibles_que` (formulario de filtros)

## Escenarios Inferidos

### Ver los posibles asistentes al ca

Pasos propuestos:
1. Abrir el formulario de búsqueda de posibles ca.
2. Elegir el centro (n o agd), el periodo y las opciones de filtro.
3. Pulsar **buscar**; hace falta haber elegido un centro.
4. Aparece el cuadro con los posibles asistentes, uno por alumno, con sus créditos y asignaturas pendientes.

Endpoints asociados:
- `/src/actividadestudios/ca_posibles_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.observ`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.idca`
- `post.na`
- `post.obj_pau`
- `post.periodo`
- `post.ref`
- `post.sel`
- `post.stack`
- `post.texto`
- `post.year`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/actividadestudios/ca_posibles_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `ca_posibles_que` o desde búsqueda de personas:

- **Legacy:** vest > posibles ca > posibles ca; vest > buscar persona > n r/dl (botón posibles CA).
- **Pills2:** vest > posibles ca > posibles ca; PERSONAS > Numerarios > Buscar n de la r/dl.
