---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Elegir centro y periodo"
flujo: "asistentes.que_ctr.gestionar.flujo"
preguntas: ["Como elegir centro y periodo en Elegir centro y periodo?"]
pantallas_principales: ["asistentes.pantalla.que_ctr_lista"]
fragmentos: ["asistentes.pantalla.que_ctr_lista"]
endpoints: ["/src/asistentes/que_ctr_lista_data"]
source: "docs/catalogo/asistentes/flujos/que_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir centro y periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir centro y periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir centro y periodo en Elegir centro y periodo?

## Donde Entrar

- Que Ctr Lista (`asistentes.pantalla.que_ctr_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir centro y periodo

1. Abrir «Actividades de personas por centros de la delegación» (pregunta «¿qué centro interesa?»).
2. Elegir **Todos los numerarios**, **Todos ctr numerarios jóvenes**, **Todos ctr numerarios mayores**, **Todos los agregados**, **Todos los de sss+** u **Otro...** (y entonces el centro concreto).
3. En «PERIODO DE INICIO O FINALIZACIÓN DE LAS ACTIVIDADES» elegir curso ca, curso crt, todo el año u **otro** (con fechas) y el año.
4. Pulsar **Buscar** (**Borrar** limpia el formulario). Si el resultado es grande, tarda unos segundos (hasta unos 10 con todos los numerarios y todo el año, lo que se considera aceptable) y muestra «Cargando...».

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/que_ctr_lista_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.que_ctr_lista`
- `asistentes.pantalla.que_ctr_lista`

## Objetivo

Elegir de qué centros se quiere ver las actividades de sus personas y en qué periodo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
