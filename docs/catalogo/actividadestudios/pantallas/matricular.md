---
id: "actividadestudios.pantalla.matricular"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matricular"
controller: "frontend/actividadestudios/controller/matricular.php"
vistas: ["frontend/actividadestudios/view/matricular.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/matricula_automatica"]
capacidades: ["actividadestudios.matricula_automatica.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Matricular

Ejecuta la matriculación automática masiva («matricular a todos») y muestra el informe de
resultado. Sucesor de `apps/actividadestudios/controller/matricular.php`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matricular.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/matricular.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/matricula_automatica` (invocado al cargar la pantalla)

## Capacidades Relacionadas

- `actividadestudios.matricula_automatica.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Al abrir la entrada de menú, el controller invoca inmediatamente `matricula_automatica` con el
POST recibido (sin formulario intermedio) y renderiza el mensaje de salida (`msg`) en un bloque
`<pre>` bajo el título «Matricular».

Es una operación batch de un solo paso: el usuario solo lee el resultado y puede volver atrás con
la navegación estándar.

## Ruta de menú

- **Legacy:** vest > buscar persona > matricular a todos
- **Pills2:** vest > buscar persona > matricular a todos; ESTUDIOS > Preparación planes estudio > Matricular a todos
