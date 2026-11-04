---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Anotar una ausencia"
flujo: "encargossacd.sacd_ausencias_get.gestionar.flujo"
preguntas: ["Como anotar una ausencia en Anotar una ausencia?"]
pantallas_principales: ["encargossacd.pantalla.sacd_ausencias_get"]
fragmentos: ["encargossacd.pantalla.sacd_ausencias_get"]
endpoints: ["/src/encargossacd/sacd_ausencias_get_data"]
source: "docs/catalogo/encargossacd/flujos/sacd_ausencias_get.md"
estado_revision: "generado"
---

# Ayuda IA - Anotar una ausencia

Usa este documento para responder preguntas de usuario sobre como trabajar con `Anotar una ausencia`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como anotar una ausencia en Anotar una ausencia?

## Donde Entrar

- Sacd Ausencias Get (`encargossacd.pantalla.sacd_ausencias_get`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Anotar una ausencia

1. Entrar en **ausencias > sacd**.
2. Elegir el **tipo** y el **sacd**.
3. En **ausencias/tareas** (columnas **fechas** y **horario**), elegir en **añadir actividades personales** el tipo (Clases del stgr, Ausencia, cv de un dia, 1ª Confesiones, boda, confirmación, 1ª comunión, bautizo, Tarea, rtm): aparece una fila con **inicio** y **fin** (al poner el inicio, el fin se rellena con la misma fecha). Con **ver anteriores** se ve el historial.
4. Pulsar **Guardar**; la pantalla vuelve atrás sola. Para borrar una, dejar en blanco la fecha de inicio y la de fin.
5. **añadir actividades personales** y **crear horario** permiten añadir otras tareas.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/sacd_ausencias_get_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ausencias_get`
- `encargossacd.pantalla.sacd_ausencias_get`

## Objetivo

Ver y anotar las ausencias o actividades personales de un sacerdote.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
