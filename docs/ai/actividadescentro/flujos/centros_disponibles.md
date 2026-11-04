---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Asignar un centro encargado"
flujo: "actividadescentro.centros_disponibles.gestionar.flujo"
preguntas: ["Como asignar un centro encargado en Asignar un centro encargado?"]
pantallas_principales: ["actividadescentro.pantalla.activ_ctr"]
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/centros_disponibles_data"]
source: "docs/catalogo/actividadescentro/flujos/centros_disponibles.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar un centro encargado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar un centro encargado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar un centro encargado en Asignar un centro encargado?

## Donde Entrar

- Activ Ctr (`actividadescentro.pantalla.activ_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar un centro encargado

1. En la fila de la actividad, pulsar **nuevo**.
2. Se listan los centros candidatos (para sg, con el nº de actividades del periodo y la distancia en días a su actividad más próxima).
3. Elegir el centro para asignarlo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadescentro/centros_disponibles_data`

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`
- `actividadescentro.pantalla.activ_ctr`

## Objetivo

Ver los centros candidatos para asignar como encargados de una actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
