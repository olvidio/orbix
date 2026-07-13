---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Plan De Misas Pantalla"
flujo: "misas.plan_de_misas_pantalla.gestionar.flujo"
preguntas: ["Como obtener datos en Plan De Misas Pantalla?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_plan_de_misas", "misas.pantalla.preparar_plan_de_misas", "misas.pantalla.ver_plan_de_misas"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
source: "docs/catalogo/misas/flujos/plan_de_misas_pantalla.md"
estado_revision: "generado"
---

# Ayuda IA - Plan De Misas Pantalla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Plan De Misas Pantalla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Plan De Misas Pantalla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_plan_de_misas`
- `misas.pantalla.preparar_plan_de_misas`
- `misas.pantalla.ver_plan_de_misas`

## Objetivo

Datos comunes para pantallas preparar/modificar/ver plan de misas: zonas, orden y tipos de plantilla en preparar.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
