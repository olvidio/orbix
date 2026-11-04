---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Consultar el plan de un sacerdote"
flujo: "misas.ver_plan_sacd.gestionar.flujo"
preguntas: ["Como consultar el plan de un sacerdote en Consultar el plan de un sacerdote?"]
pantallas_principales: ["misas.pantalla.ver_plan_sacd"]
fragmentos: ["misas.pantalla.ver_plan_sacd"]
endpoints: ["/src/misas/ver_plan_sacd_data"]
source: "docs/catalogo/misas/flujos/ver_plan_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar el plan de un sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar el plan de un sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el plan de un sacerdote en Consultar el plan de un sacerdote?

## Donde Entrar

- Ver Plan Sacd (`misas.pantalla.ver_plan_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el plan de un sacerdote

1. En **Plan sacerdote**, elegir sacerdote y periodo.
2. La tabla muestra **Dia**, **Encargo** y **Observaciones** de cada misa.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_plan_sacd_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_plan_sacd`
- `misas.pantalla.ver_plan_sacd`

## Objetivo

Mostrar, día a día, las misas asignadas a un sacerdote en un periodo.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
