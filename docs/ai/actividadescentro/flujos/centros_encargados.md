---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Centros Encargados"
flujo: "actividadescentro.centros_encargados.gestionar.flujo"
preguntas: ["Como obtener datos en Centros Encargados?"]
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
endpoints: ["/src/actividadescentro/centros_encargados_data"]
source: "docs/catalogo/actividadescentro/flujos/centros_encargados.md"
estado_revision: "generado"
---

# Ayuda IA - Centros Encargados

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centros Encargados`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Centros Encargados?

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

Tras asignar, reordenar o eliminar un centro encargado, la celda de esa actividad se refresca con la lista actualizada de centros y el flag `permite_modificar` (que decide si cada centro se pinta como enlace o como texto plano). Es un paso automático, no una acción explícita del usuario.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
