---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Lista Actividades Ctr"
flujo: "actividadescentro.lista_actividades_ctr.gestionar.flujo"
preguntas: ["Como obtener datos en Lista Actividades Ctr?"]
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/lista_actividades_ctr_data"]
source: "docs/catalogo/actividadescentro/flujos/lista_actividades_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Lista Actividades Ctr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Lista Actividades Ctr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Lista Actividades Ctr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`

## Objetivo

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de actividades del colectivo (`tipo`) en ese periodo y, por cada una, los centros encargados actuales y los flags de permiso (modificar / crear centros) que deciden qué acciones se ofrecen.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
