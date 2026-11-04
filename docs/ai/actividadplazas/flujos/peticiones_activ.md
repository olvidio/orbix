---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Abrir las peticiones de una persona"
flujo: "actividadplazas.peticiones_activ.gestionar.flujo"
preguntas: ["Como abrir las peticiones de una persona en Abrir las peticiones de una persona?"]
pantallas_principales: ["actividadplazas.pantalla.peticiones_activ"]
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
endpoints: ["/src/actividadplazas/peticiones_activ_data"]
source: "docs/catalogo/actividadplazas/flujos/peticiones_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir las peticiones de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir las peticiones de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir las peticiones de una persona en Abrir las peticiones de una persona?

## Donde Entrar

- Peticiones Activ (`actividadplazas.pantalla.peticiones_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir las peticiones de una persona

1. Desde un listado de personas (n, a o agd), seleccionar a la persona.
2. Aparecen las actividades que puede pedir y las peticiones que ya tenía; las que ya no están disponibles se descartan.
3. Los desplegables salen con el orden de prioridad guardado. Se pueden cambiar y guardar, o borrar todo (ver «Guardar las peticiones de una persona»).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_activ_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.peticiones_activ`
- `actividadplazas.pantalla.peticiones_activ`

## Objetivo

Ver las peticiones de plaza de una persona: su nombre, las actividades disponibles del tipo elegido y las peticiones ya guardadas, listas para reordenar o ampliar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
