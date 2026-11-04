---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Consultar el plan de un centro"
flujo: "misas.ver_plan_ctr.gestionar.flujo"
preguntas: ["Como consultar el plan de un centro en Consultar el plan de un centro?"]
pantallas_principales: ["misas.pantalla.ver_plan_ctr"]
fragmentos: ["misas.pantalla.imprimir_plan_ctr", "misas.pantalla.ver_plan_ctr"]
endpoints: ["/src/misas/ver_plan_ctr_data"]
source: "docs/catalogo/misas/flujos/ver_plan_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar el plan de un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar el plan de un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el plan de un centro en Consultar el plan de un centro?

## Donde Entrar

- Ver Plan Ctr (`misas.pantalla.ver_plan_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el plan de un centro

1. En **Plan centro**, elegir zona, centro y periodo.
2. Leer la cuadrícula; las iniciales de la leyenda identifican a cada sacerdote.
3. Se puede imprimir desde la propia pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_plan_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_plan_ctr`
- `misas.pantalla.imprimir_plan_ctr`
- `misas.pantalla.ver_plan_ctr`

## Objetivo

Mostrar el plan de misas de un centro: encargos en filas y días en columnas, con leyenda de sacerdotes.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
