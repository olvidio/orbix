---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Elegir la delegación propietaria de la plaza"
flujo: "actividadplazas.posibles_propietarios.gestionar.flujo"
preguntas: ["Como elegir la delegación propietaria de la plaza en Elegir la delegación propietaria de la plaza?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadplazas/posibles_propietarios_data"]
source: "docs/catalogo/actividadplazas/flujos/posibles_propietarios.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir la delegación propietaria de la plaza

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir la delegación propietaria de la plaza`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir la delegación propietaria de la plaza en Elegir la delegación propietaria de la plaza?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir la delegación propietaria de la plaza

1. Abrir el formulario de asistencia, bien desde una persona o bien desde una actividad.
2. En **propiedad de** aparecen solo las delegaciones posibles para esa combinación; por defecto sale preseleccionada la propia delegación.
3. Elegir la que corresponda y guardar el formulario.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/posibles_propietarios_data`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Al editar la asistencia de una persona a una actividad, elegir qué delegación es la propietaria de la plaza entre las opciones válidas para esa persona y esa actividad.

## Errores Documentados

- `faltan parametros id_nom / id_activ`
- `No se encuentra persona con id_nom <id>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
