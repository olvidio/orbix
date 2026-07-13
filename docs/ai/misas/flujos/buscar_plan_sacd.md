---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Buscar Plan Sacd"
flujo: "misas.buscar_plan_sacd.gestionar.flujo"
preguntas: ["Como obtener datos en Buscar Plan Sacd?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.buscar_plan_sacd"]
endpoints: ["/src/misas/buscar_plan_sacd_data"]
source: "docs/catalogo/misas/flujos/buscar_plan_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar Plan Sacd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar Plan Sacd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Buscar Plan Sacd?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.buscar_plan_sacd`

## Objetivo

Devuelve el desplegable de sacerdotes para el buscador del plan SACD, filtrado por rol y zona del usuario.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
