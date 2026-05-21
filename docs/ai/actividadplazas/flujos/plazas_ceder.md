---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Plazas Ceder"
flujo: "actividadplazas.plazas_ceder.gestionar.flujo"
preguntas: ["Como ejecutar en Plazas Ceder?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
endpoints: ["/src/actividadplazas/plazas_ceder"]
source: "docs/catalogo/actividadplazas/flujos/plazas_ceder.md"
estado_revision: "generado"
---

# Ayuda IA - Plazas Ceder

Usa este documento para responder preguntas de usuario sobre como trabajar con `Plazas Ceder`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Plazas Ceder?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.resumen_plazas`

## Objetivo

Gestiona PlazasCeder. Actualiza el array cedidas de ActividadPlazasDl para ceder (o quitar) plazas de mi_dele a otra dl en una actividad.

## Errores Documentados

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
