---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Lista Actividades Sacd"
flujo: "actividadessacd.lista_actividades_sacd.gestionar.flujo"
preguntas: ["Como obtener datos en Lista Actividades Sacd?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/lista_actividades_sacd_data"]
source: "docs/catalogo/actividadessacd/flujos/lista_actividades_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Lista Actividades Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Lista Actividades Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Lista Actividades Sacd?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Elegir periodo (año + trimestre o rango libre) en la barra de filtros.
2. Pulsar **buscar**.
3. El sistema construye la tabla con actividades, sacd encargados y leyenda de colores.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/lista_actividades_sacd_data`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de actividades del tipo (`na` / `sg` / `sr` / `sssc` / `sf` / variantes `sf_*` / `falta_sacd` / `solape`) en ese periodo y, por cada una, los sacd encargados actuales y los flags de permiso que deciden qué acciones se ofrecen (asignar, reordenar, borrar).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
