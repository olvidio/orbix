---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Peticiones Activ"
flujo: "actividadplazas.peticiones_activ.gestionar.flujo"
preguntas: ["Como obtener datos en Peticiones Activ?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.peticiones_activ"]
endpoints: ["/src/actividadplazas/peticiones_activ_data"]
source: "docs/catalogo/actividadplazas/flujos/peticiones_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Peticiones Activ

Usa este documento para responder preguntas de usuario sobre como trabajar con `Peticiones Activ`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Peticiones Activ?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Desde un listado de personas (n / a / agd), abrir las peticiones de plaza de una persona.
2. El sistema carga `peticiones_activ_data` con `id_nom` y `sactividad`.
3. Devuelve las actividades candidatas y las peticiones actuales; limpia peticiones antiguas ya no
4. Pinta los desplegables (`DesplegableArray`) precargados con el orden de prioridad; el usuario

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/peticiones_activ_data`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.peticiones_activ`

## Objetivo

Consultar y preparar la edición de las peticiones de plaza de una persona: ver su nombre, las actividades disponibles del tipo y las peticiones ya guardadas, listas para reordenar o ampliar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
