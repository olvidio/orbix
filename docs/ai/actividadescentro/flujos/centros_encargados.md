---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Ver los centros encargados actualizados"
flujo: "actividadescentro.centros_encargados.gestionar.flujo"
preguntas: ["Como ver los centros encargados actualizados en Ver los centros encargados actualizados?"]
pantallas_principales: ["actividadescentro.pantalla.activ_ctr"]
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/centros_encargados_data"]
source: "docs/catalogo/actividadescentro/flujos/centros_encargados.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los centros encargados actualizados

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los centros encargados actualizados`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los centros encargados actualizados en Ver los centros encargados actualizados?

## Donde Entrar

- Activ Ctr (`actividadescentro.pantalla.activ_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los centros encargados actualizados

1. Asignar, reordenar o quitar un centro.
2. La celda de la actividad se refresca sola con la lista al día.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadescentro/centros_encargados_data`

## Pantallas Y Fragmentos Relacionados

- `actividadescentro.pantalla.activ_ctr`
- `actividadescentro.pantalla.activ_ctr`

## Objetivo

Refrescar la lista de centros encargados de una actividad tras asignar, reordenar o quitar uno.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
