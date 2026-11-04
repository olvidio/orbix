---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Buscar cartas de presentación"
flujo: "cartaspresentacion.cartas_presentacion_buscar.gestionar.flujo"
preguntas: ["Como buscar cartas de presentación en Buscar cartas de presentación?"]
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion_buscar"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_lista"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_buscar_data", "/src/cartaspresentacion/cartas_presentacion_lista_data"]
source: "docs/catalogo/cartaspresentacion/flujos/cartas_presentacion_buscar.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar cartas de presentación

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar cartas de presentación`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar cartas de presentación en Buscar cartas de presentación?

## Donde Entrar

- Cartas Presentacion Buscar (`cartaspresentacion.pantalla.cartas_presentacion_buscar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar cartas de presentación

1. Abrir **buscar**: se cargan los desplegables de filtro.
2. Rellenar los filtros y pulsar **buscar**.
3. Los resultados aparecen debajo, en el mismo formato que las listas.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/cartas_presentacion_buscar_data`
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion_buscar`
- `cartaspresentacion.pantalla.cartas_presentacion_lista`

## Objetivo

Encontrar cartas de presentación según criterios geográficos o de delegación.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
