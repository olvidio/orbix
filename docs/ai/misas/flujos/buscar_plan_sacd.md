---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver el plan de un sacerdote"
flujo: "misas.buscar_plan_sacd.gestionar.flujo"
preguntas: ["Como ver el plan de un sacerdote en Ver el plan de un sacerdote?"]
pantallas_principales: ["misas.pantalla.buscar_plan_sacd"]
fragmentos: ["misas.pantalla.buscar_plan_sacd"]
endpoints: ["/src/misas/buscar_plan_sacd_data"]
source: "docs/catalogo/misas/flujos/buscar_plan_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el plan de un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el plan de un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el plan de un sacerdote en Ver el plan de un sacerdote?

## Donde Entrar

- Buscar Plan Sacd (`misas.pantalla.buscar_plan_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el plan de un sacerdote

1. Entrar en **Plan sacerdote**.
2. Elegir el **Sacerdote** (la lista se limita según el rol y la zona del usuario) y el **Periodo**.
3. Se muestra la lista día a día de las misas asignadas.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/buscar_plan_sacd_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.buscar_plan_sacd`
- `misas.pantalla.buscar_plan_sacd`

## Objetivo

Elegir sacerdote y periodo para ver su plan de misas.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
