---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver el plan de misas de un centro"
flujo: "misas.buscar_plan_ctr.gestionar.flujo"
preguntas: ["Como ver el plan de misas de un centro en Ver el plan de misas de un centro?"]
pantallas_principales: ["misas.pantalla.buscar_plan_ctr"]
fragmentos: ["misas.pantalla.buscar_plan_ctr"]
endpoints: ["/src/misas/buscar_plan_ctr_data"]
source: "docs/catalogo/misas/flujos/buscar_plan_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el plan de misas de un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el plan de misas de un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el plan de misas de un centro en Ver el plan de misas de un centro?

## Donde Entrar

- Buscar Plan Ctr (`misas.pantalla.buscar_plan_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el plan de misas de un centro

1. Entrar en **Plan centro**.
2. Elegir la **zona**, el **centro** y el **Periodo**.
3. Se muestra el plan: los encargos en filas y los días en columnas, con la leyenda de sacerdotes.
4. Si el usuario no tiene permiso, aparece «No tiene permiso para ver esta página».

Referencias tecnicas para verificar la respuesta:
- `/src/misas/buscar_plan_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.buscar_plan_ctr`
- `misas.pantalla.buscar_plan_ctr`

## Objetivo

Elegir zona, centro y periodo para ver el plan de misas de un centro.

## Errores Documentados

- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
