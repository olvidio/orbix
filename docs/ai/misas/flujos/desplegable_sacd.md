---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Elegir sacerdote en una celda"
flujo: "misas.desplegable_sacd.gestionar.flujo"
preguntas: ["Como elegir sacerdote en una celda en Elegir sacerdote en una celda?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/misas/desplegable_sacd"]
source: "docs/catalogo/misas/flujos/desplegable_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir sacerdote en una celda

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir sacerdote en una celda`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir sacerdote en una celda en Elegir sacerdote en una celda?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir sacerdote en una celda

1. En **Modificar plan**, pulsar la celda del día y encargo.
2. En **Selección de sacd** marcar qué sacerdotes se quieren ver: **libre a 1a hora**, **zona** (por defecto), **dl** o **de paso**; después elegir el **Sacerdote** de la lista, que solo muestra los disponibles ese día.
3. Indicar **Inicio**, **Fin** y **Observaciones** si procede y pulsar **Save**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/desplegable_sacd`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Ofrecer, al pulsar una celda de la cuadrícula, los sacerdotes disponibles ese día.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
