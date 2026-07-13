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

1. Abrir el resumen de plazas de la actividad.
2. En el bloque **Ceder**, escribir el número de plazas y elegir la delegación destino.
3. Pulsar **Guardar** (`fnjs_guardar`): envía `id_activ`, `num_plazas` y `region_dl` a
4. Si tiene éxito, la pantalla se recarga (`fnjs_actualizar`) mostrando el nuevo reparto.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/plazas_ceder`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.resumen_plazas`

## Objetivo

Desde el resumen de plazas de una actividad, indicar cuántas plazas ceder a otra delegación y confirmar el cambio.

## Errores Documentados

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
