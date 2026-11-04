---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Elegir las casas y el periodo del planning"
flujo: "planning.planning_casa_que.gestionar.flujo"
preguntas: ["Como elegir las casas y el periodo del planning en Elegir las casas y el periodo del planning?"]
pantallas_principales: ["planning.pantalla.planning_casa_que"]
fragmentos: []
endpoints: ["/src/planning/planning_casa_que_data"]
source: "docs/catalogo/planning/flujos/planning_casa_que.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir las casas y el periodo del planning

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir las casas y el periodo del planning`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir las casas y el periodo del planning en Elegir las casas y el periodo del planning?

## Donde Entrar

- Planning por casas (filtros) (`planning.pantalla.planning_casa_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir las casas y el periodo del planning

1. Abrir «BÚSQUEDA DE CASAS CUYO PLANNING INTERESA».
2. Elegir el grupo de casas: **casas sólo sv**, **casas sólo sf**, **casas comunes**, **casas sv**, **casas sf**, **casas y ctr sf**, **una casa o lugar** (y elegirla en el desplegable), **todas las actividades sv** o **todas las actividades sf**.
3. Indicar si se quieren incluir las **casas sin actividad** (no / sí).
4. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
5. Pulsar **Planning vista tabla** (calendario en pantalla) o **vista para imprimir**.

Referencias tecnicas para verificar la respuesta:
- `/src/planning/planning_casa_que_data`

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_casa_que`

## Objetivo

Elegir de qué casas se quiere ver el calendario de ocupación y en qué periodo.

## Errores Documentados

- `No se encuentra el usuario`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
