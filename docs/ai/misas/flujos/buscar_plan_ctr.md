---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Buscar Plan Ctr"
flujo: "misas.buscar_plan_ctr.gestionar.flujo"
preguntas: ["Como obtener datos en Buscar Plan Ctr?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.buscar_plan_ctr"]
endpoints: ["/src/misas/buscar_plan_ctr_data"]
source: "docs/catalogo/misas/flujos/buscar_plan_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar Plan Ctr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar Plan Ctr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Buscar Plan Ctr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.buscar_plan_ctr`

## Objetivo

Inicializa el formulario de búsqueda del plan CTR: zonas, centros disponibles y selección por defecto según rol del usuario.

## Errores Documentados

- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
