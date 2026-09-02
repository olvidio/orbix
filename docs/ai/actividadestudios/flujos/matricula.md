---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matricula"
flujo: "actividadestudios.matricula.gestionar.flujo"
preguntas: ["Como crear en Matricula?", "Como eliminar en Matricula?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona", "actividadestudios.pantalla.matriculas_lista", "actividadestudios.pantalla.matriculas_pendientes"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matricula_nueva"]
source: "docs/catalogo/actividadestudios/flujos/matricula.md"
estado_revision: "generado"
---

# Ayuda IA - Matricula

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matricula`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Matricula?
- Como eliminar en Matricula?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Desde la lista de asistentes, seleccionar uno y **plan estudios**.
2. Pulsar **matricular en una asignatura** (primero de los botones). El desplegable
   solo muestra asignaturas que ya están en el CA y en las que el alumno aún no está matriculado.
3. Elegir asignatura (y preceptor si aplica) y **guardar**.
4. **añadir asignatura** abre el catálogo del plan. Si esa asignatura ya está en el CA,
   aparece el aviso de duplicado; al continuar se matricula y se crea una nueva oferta
   en esta dl (preceptor u otro profesor). En el listado 1303, si la asignatura está
   duplicada (otra dl), el nombre lleva delante la sigla `(dlxx)`. **matricular automáticamente** cubre el lote según el plan.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matricula_nueva`
- `/src/actividadestudios/form_matriculas_de_una_persona_data`

## Eliminar

1. En un listado de matrículas, seleccionar una o varias filas.
2. Pulsar **borrar matrícula** y confirmar.
3. El sistema elimina las matrículas y refresca el listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matricula_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_matriculas_de_una_persona`
- `actividadestudios.pantalla.matriculas_lista`
- `actividadestudios.pantalla.matriculas_pendientes`

## Objetivo

El usuario crea una matrícula (persona + asignatura + nivel en una actividad) o elimina una o varias matrículas seleccionadas desde listados o formularios de dossier.

## Errores Documentados

- `falta id_activ o id_nom`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no encuentro asignatura para ese nivel`
- `no encuentro la matricula`
- `debe poner una asignatura`
- `Ya existe esta asignatura en esta actividad. Solamente debería continuar si quiere hacerla con preceptor u otro profesor`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
