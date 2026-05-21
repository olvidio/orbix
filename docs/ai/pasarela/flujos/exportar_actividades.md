---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "pasarela"
titulo: "Exportar Actividades"
flujo: "pasarela.exportar_actividades.gestionar.flujo"
preguntas: ["Como obtener datos en Exportar Actividades?"]
pantallas_principales: []
fragmentos: ["pasarela.pantalla.exportar_select"]
endpoints: ["/src/pasarela/exportar_actividades_data"]
source: "docs/catalogo/pasarela/flujos/exportar_actividades.md"
estado_revision: "generado"
---

# Ayuda IA - Exportar Actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Exportar Actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Exportar Actividades?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `pasarela.pantalla.exportar_select`

## Objetivo

Gestiona ExportarActividades. Caso de uso "exportar actividades": dado un filtro (tipo de actividad, periodo y casas), devuelve cabeceras + filas para el listado de exportación, mezclando datos de actividades con las conversiones de pasarela. Devuelve un array serializable por {.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
