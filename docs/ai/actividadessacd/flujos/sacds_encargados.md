---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadessacd"
titulo: "Sacds Encargados"
flujo: "actividadessacd.sacds_encargados.gestionar.flujo"
preguntas: ["Como obtener datos en Sacds Encargados?"]
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
endpoints: ["/src/actividadessacd/sacds_encargados_data"]
source: "docs/catalogo/actividadessacd/flujos/sacds_encargados.md"
estado_revision: "generado"
---

# Ayuda IA - Sacds Encargados

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacds Encargados`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Sacds Encargados?

## Donde Entrar

- Activ Sacd (`actividadessacd.pantalla.activ_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Tras una asignación, reordenación o borrado, el sistema actualiza la celda `<id_activ>_sacds`.
2. Se repintan los sacd encargados con sus enlaces de menú contextual.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadessacd/sacds_encargados_data`

## Pantallas Y Fragmentos Relacionados

- `actividadessacd.pantalla.activ_sacd`

## Objetivo

Tras asignar, reordenar o borrar un sacd, el sistema refresca la celda de sacd de la actividad consultando los encargados actuales y los flags de permiso que deciden si se muestran como enlaces interactivos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
