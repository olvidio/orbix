---
id: "actividadestudios.matricula_automatica.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Matricular a todos automáticamente"
capacidad: "actividadestudios.matricula_automatica.gestionar"
pantallas_principales: ["actividadestudios.pantalla.matricular"]
fragmentos: ["actividadestudios.pantalla.matricular"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/matricula_automatica"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matricula Automatica

Matriculación masiva según plan de estudios vigente.

## Objetivo De Usuario

Matricular automáticamente en las asignaturas del ca o la cv a todas las personas que tienen actividad de estudios asignada.

## Punto De Entrada

Menú **vest > buscar persona > matricular a todos** (Pills2: ESTUDIOS > Preparación planes estudio).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matricular`

## Escenarios Inferidos

### Matricular a todos automáticamente

Pasos propuestos:
1. Al pulsar la opción de menú sale primero un aviso: «Se van a matricular automáticamente todas las personas. Si el plan de estudios de una persona no está confirmado, se borrarán sus matrículas anteriores. ¿Quiere continuar?». Con **volver** no se hace nada; con **continuar** se ejecuta la matriculación para todas las personas, sin más confirmación.
2. Para cada persona con ca o cv asignado, el sistema recalcula sus asignaturas (respetando las ya aprobadas y los topes de opcionales) y crea las matrículas; si el plan no está confirmado, borra antes las matrículas previas.
3. La pantalla muestra un mensaje por cada persona con la que no se hizo nada, por ejemplo «no se ha hecho nada con Sánchez Andreu, Enrique: no tiene asignado ca».

Endpoints asociados:
- `/src/actividadestudios/matricula_automatica`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado (acepta POST completo del contexto de persona).

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/matricula_automatica`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > buscar persona > matricular a todos.
- **Pills2:** vest > buscar persona > matricular a todos; ESTUDIOS > Preparación planes estudio >
  Matricular a todos.
