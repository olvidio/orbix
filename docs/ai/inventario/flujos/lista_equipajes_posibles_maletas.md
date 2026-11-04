---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Crear una valija nueva"
flujo: "inventario.lista_equipajes_posibles_maletas.gestionar.flujo"
preguntas: ["Como crear una valija nueva en Crear una valija nueva?"]
pantallas_principales: ["inventario.pantalla.equipajes_posibles_maletas"]
fragmentos: ["inventario.pantalla.equipajes_posibles_maletas"]
endpoints: ["/src/inventario/lista_equipajes_posibles_maletas"]
source: "docs/catalogo/inventario/flujos/lista_equipajes_posibles_maletas.md"
estado_revision: "generado"
---

# Ayuda IA - Crear una valija nueva

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear una valija nueva`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear una valija nueva en Crear una valija nueva?

## Donde Entrar

- Elegir maleta/grupo (`inventario.pantalla.equipajes_posibles_maletas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear una valija nueva

1. En **hacer equipajes**, elegir el equipaje.
2. Pulsar **nueva valija**.
3. El sistema propone el siguiente número («valija» + número) para la nueva maleta.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_equipajes_posibles_maletas`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_posibles_maletas`
- `inventario.pantalla.equipajes_posibles_maletas`

## Objetivo

Calcular el nombre de la siguiente valija (maleta) que se puede crear en un equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
