---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Procesos Select"
flujo: "procesos.procesos_select.gestionar.flujo"
preguntas: ["Como obtener datos en Procesos Select?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.procesos_select"]
endpoints: ["/src/procesos/procesos_select_data"]
source: "docs/catalogo/procesos/flujos/procesos_select.md"
estado_revision: "generado"
---

# Ayuda IA - Procesos Select

Usa este documento para responder preguntas de usuario sobre como trabajar con `Procesos Select`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Procesos Select?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.procesos_select`

## Objetivo

Gestiona ProcesosSelect. Caso de uso: datos para la pantalla procesos_select. Devuelve las opciones del desplegable de tipo de proceso para que la vista frontend monte el frontend\shared\web\Desplegable y los web\Hash correspondientes.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
