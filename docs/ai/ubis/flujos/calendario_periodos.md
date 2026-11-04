---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Consultar los periodos de una casa"
flujo: "ubis.calendario_periodos.gestionar.flujo"
preguntas: ["Como consultar los periodos de una casa en Consultar los periodos de una casa?", "Como borrar un periodo en Consultar los periodos de una casa?"]
pantallas_principales: ["ubis.pantalla.calendario_periodos"]
fragmentos: []
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
source: "docs/catalogo/ubis/flujos/calendario_periodos.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los periodos de una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los periodos de una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los periodos de una casa en Consultar los periodos de una casa?
- Como borrar un periodo en Consultar los periodos de una casa?

## Donde Entrar

- Calendario Periodos (`ubis.pantalla.calendario_periodos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los periodos de una casa

1. Entrar en **Definir periodos**.
2. Elegir la casa y el año y pulsar **buscar**.
3. Se listan los periodos ordenados por fecha de inicio y se avisa si alguno se solapa con otro.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Borrar un periodo

1. En la lista, pulsar eliminar en el periodo.
2. Confirmar «¿Está seguro de borrar este periodo?».

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.calendario_periodos`

## Objetivo

Definir los periodos del calendario de una casa en un año y borrar los que sobran.

## Errores Documentados

- `no sé cuál he de borar`
- `no se encuentra el periodo a borrar`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
