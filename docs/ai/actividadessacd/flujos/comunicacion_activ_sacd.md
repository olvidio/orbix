---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Comunicacion Activ Sacd"
flujo: "actividadessacd.comunicacion_activ_sacd.gestionar.flujo"
preguntas: ["Como obtener datos en Comunicacion Activ Sacd?"]
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
endpoints: ["/src/actividadessacd/comunicacion_activ_sacd_data"]
source: "docs/catalogo/actividadessacd/flujos/comunicacion_activ_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Comunicacion Activ Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Comunicacion Activ Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Comunicacion Activ Sacd?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Seleccionar periodo en la barra de filtros (o entrar con un sacd preseleccionado).
2. Pulsar **buscar** (o auto-carga si `AUTO_CARGAR`).
3. El sistema pinta el listado por sacd con actividades, textos y leyenda.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/comunicacion_activ_sacd_data`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.com_sacd_activ_periodo`

## Objetivo

El usuario selecciona un periodo y pulsa **buscar**: el sistema construye, por cada sacd, la lista de actividades a comunicar (incluidas las de los "sacd de paso" cuando procede) con los textos de la carta y las cabeceras de columnas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
