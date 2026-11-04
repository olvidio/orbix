---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Publicar actividades"
flujo: "actividades.actividad_publicar.gestionar.flujo"
preguntas: ["Como publicar actividades en Publicar actividades?"]
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_select"]
endpoints: ["/src/actividades/actividad_publicar"]
source: "docs/catalogo/actividades/flujos/actividad_publicar.md"
estado_revision: "generado"
---

# Ayuda IA - Publicar actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Publicar actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como publicar actividades en Publicar actividades?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Publicar actividades

1. Abrir **publicar activ** del menú.
2. Buscar las actividades de la propia dl que aún no están publicadas.
3. En la lista, marcar las filas y pulsar **publicar**.
4. Comprobar en el listado que ya aparecen como publicadas.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_publicar`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_que`
- `actividades.pantalla.actividad_select`

## Objetivo

Buscar actividades que aún no están publicadas y publicarlas de golpe.

## Errores Documentados

- `hay un error, no se ha guardado + detalle (por id fallido)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
