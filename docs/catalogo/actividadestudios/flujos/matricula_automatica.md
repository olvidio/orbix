---
id: "actividadestudios.matricula_automatica.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matricula Automatica"
capacidad: "actividadestudios.matricula_automatica.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matricular"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/matricula_automatica"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matricula Automatica

Matriculación masiva según plan de estudios vigente.

## Objetivo De Usuario

El usuario ejecuta la matriculación automática de una o todas las personas activas: el sistema
determina la actividad de estudios vigente (`ca-n`, `cv-agd`), recalcula asignaturas
matriculables respetando aprobadas y topes de opcionales, y crea las matrículas. Sustituye
`apps/actividadestudios/controller/matricular.php`.

## Punto De Entrada

Pantalla `matricular` (`frontend/actividadestudios/controller/matricular.php`): al abrirse
(o al enviar el formulario POST) llama a `matricula_automatica` vía PostRequest y muestra
el mensaje de resultado.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matricular`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Abrir **matricular a todos** desde el menú (o desde búsqueda de persona con selección).
2. El sistema recibe `id_pau`/`sel` (persona concreta) o procesa todas las personas activas.
3. Para cada persona, borra matrículas previas si el plan no está confirmado y recalcula.
4. Se muestra el mensaje resumen en `matricular.phtml`.

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
