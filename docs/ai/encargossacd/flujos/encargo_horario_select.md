---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Encargo Horario Select"
flujo: "encargossacd.encargo_horario_select.gestionar.flujo"
preguntas: ["Como obtener datos en Encargo Horario Select?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_horario_select"]
endpoints: ["/src/encargossacd/encargo_horario_select_data"]
source: "docs/catalogo/encargossacd/flujos/encargo_horario_select.md"
estado_revision: "generado"
---

# Ayuda IA - Encargo Horario Select

Usa este documento para responder preguntas de usuario sobre como trabajar con `Encargo Horario Select`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Encargo Horario Select?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_horario_select`

## Objetivo

Gestiona EncargoHorarioSelect. Datos para la lista de horarios de un encargo (encargo_horario_select). Se devuelven ya precalculados el texto descriptivo del horario y las fechas formateadas para que el frontend solo arme frontend\shared\web\Lista.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
