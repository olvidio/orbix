---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Actividad Asignatura"
flujo: "actividadestudios.actividad_asignatura.gestionar.flujo"
preguntas: ["Como crear en Actividad Asignatura?", "Como eliminar en Actividad Asignatura?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/actividad_asignatura_eliminar", "/src/actividadestudios/actividad_asignatura_nueva"]
source: "docs/catalogo/actividadestudios/flujos/actividad_asignatura.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Actividad Asignatura?
- Como eliminar en Actividad Asignatura?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. En el dossier 3005 de una actividad, pulsar **nuevo** para abrir el formulario de alta.
2. Elegir asignatura, profesor, fechas y tipo; pulsar **guardar**.
3. Si esa asignatura ya está en la actividad (incluida otra dl), aparece un aviso:
   «Ya existe esta asignatura en esta actividad. Solamente debería continuar si quiere hacerla con preceptor u otro profesor». Cancelar vuelve al listado; aceptar guarda.
4. Si no hay duplicado (o se confirmó), el sistema crea la `ActividadAsignatura` y abre el dossier 3005.
   En el listado, si la misma asignatura está en más de una dl, el nombre lleva delante
   la sigla entre paréntesis (`(dlxx) …`) para distinguirlas.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_nueva`

## Eliminar

1. En el listado de asignaturas del dossier 3005, seleccionar una fila.
2. Pulsar **quitar asignatura** y confirmar el aviso genérico.
3. Si hay alumnos matriculados, aparece un segundo aviso con el número y las dl
   (p. ej. «Hay 3 alumnos matriculados (dlbv: 2, dlxx: 1). Si continúa se borrarán
   también esas matrículas.»). Cancelar no borra nada; aceptar elimina la oferta
   y esas matrículas de esta dl.
4. Si no hay matriculados, se elimina solo la asignatura impartida y se refresca el listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

El usuario crea una nueva asignatura impartida en la actividad (profesor, fechas, tipo) o elimina una existente desde el dossier de asignaturas. Si al quitar hay matriculados, se advierte (cuántos y de qué dl) y al confirmar se borran también esas matrículas. Sustituye los cases `nuevo` y `eliminar` del antiguo `update_3005.php`.

## Errores Documentados

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha borrado`
- `hay un error, no se ha creado`
- `no encuentro la asignatura`
- `sólo se puede eliminar una asignatura desde el dossier de la actividad`
- `Ya existe esta asignatura en esta actividad. Solamente debería continuar si quiere hacerla con preceptor u otro profesor`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
