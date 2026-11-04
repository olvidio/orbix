---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Ceder plazas a otra delegación"
flujo: "actividadplazas.plazas_ceder.gestionar.flujo"
preguntas: ["Como ceder plazas a otra delegación en Ceder plazas a otra delegación?"]
pantallas_principales: ["actividadplazas.pantalla.resumen_plazas"]
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
endpoints: ["/src/actividadplazas/plazas_ceder"]
source: "docs/catalogo/actividadplazas/flujos/plazas_ceder.md"
estado_revision: "generado"
---

# Ayuda IA - Ceder plazas a otra delegación

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ceder plazas a otra delegación`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ceder plazas a otra delegación en Ceder plazas a otra delegación?

## Donde Entrar

- Resumen Plazas (`actividadplazas.pantalla.resumen_plazas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ceder plazas a otra delegación

1. Abrir el resumen de plazas de la actividad.
2. En el bloque **Ceder**, escribir el número de plazas y elegir la delegación que las recibe.
3. Pulsar **Guardar**.
4. La pantalla se recarga y muestra el nuevo reparto.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/plazas_ceder`

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.resumen_plazas`
- `actividadplazas.pantalla.resumen_plazas`

## Objetivo

Desde el resumen de plazas de una actividad, indicar cuántas plazas se ceden a otra delegación y confirmar el cambio.

## Errores Documentados

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
