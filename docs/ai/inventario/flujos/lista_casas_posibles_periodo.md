---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Consultar las casas del periodo"
flujo: "inventario.lista_casas_posibles_periodo.gestionar.flujo"
preguntas: ["Como elegir la casa del equipaje en Consultar las casas del periodo?"]
pantallas_principales: ["inventario.pantalla.equipajes_casas_posibles"]
fragmentos: ["inventario.pantalla.equipajes_casas_posibles"]
endpoints: ["/src/inventario/lista_casas_posibles_periodo"]
source: "docs/catalogo/inventario/flujos/lista_casas_posibles_periodo.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar las casas del periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar las casas del periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la casa del equipaje en Consultar las casas del periodo?

## Donde Entrar

- Casas posibles (`inventario.pantalla.equipajes_casas_posibles`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la casa del equipaje

1. En **nuevo equipaje**, elegir el periodo.
2. Pulsar **ver las casas donde se realizan las actividades**.
3. Elegir la casa en el desplegable: se carga la lista de sus actividades.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_casas_posibles_periodo`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_casas_posibles`
- `inventario.pantalla.equipajes_casas_posibles`

## Objetivo

Ver en qué casas se realizan actividades en el periodo elegido, para escoger la casa del equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
