---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Actividades Buscar"
flujo: "notas.actividades_buscar.gestionar.flujo"
preguntas: ["Como obtener datos en Actividades Buscar?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.actividad_buscar_form"]
endpoints: ["/src/notas/actividades_buscar_data"]
source: "docs/catalogo/notas/flujos/actividades_buscar.md"
estado_revision: "generado"
---

# Ayuda IA - Actividades Buscar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividades Buscar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Actividades Buscar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.actividad_buscar_form`

## Objetivo

Gestiona ActividadesBuscar. Datos (delegaciones + actividades) para el dialogo "buscar actividad" que abre frontend/notas/controller/actividad_buscar_form.php desde form_notas_de_una_persona.phtml al modificar una nota asociada a una actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
