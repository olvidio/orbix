---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar las actividades de un equipaje"
flujo: "inventario.equipajes_lista_activ_equipaje.gestionar.flujo"
preguntas: ["Como ver las actividades del equipaje impreso en Consultar las actividades de un equipaje?"]
pantallas_principales: ["inventario.pantalla.equipajes_imprimir"]
fragmentos: ["inventario.pantalla.equipajes_imprimir"]
endpoints: ["/src/inventario/equipajes_lista_activ_equipaje"]
source: "docs/catalogo/inventario/flujos/equipajes_lista_activ_equipaje.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar las actividades de un equipaje

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar las actividades de un equipaje`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las actividades del equipaje impreso en Consultar las actividades de un equipaje?

## Donde Entrar

- Imprimir equipaje (`inventario.pantalla.equipajes_imprimir`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las actividades del equipaje impreso

1. Abrir **imprimir equipajes** y elegir el equipaje.
2. Bajo el título «Inventario de publicaciones internas» aparecen las actividades del equipaje con sus fechas.
3. No hay nada que rellenar: se genera solo.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_lista_activ_equipaje`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_imprimir`
- `inventario.pantalla.equipajes_imprimir`

## Objetivo

Mostrar, en el equipaje impreso, las actividades que lo forman.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
