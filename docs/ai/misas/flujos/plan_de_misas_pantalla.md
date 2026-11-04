---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Elegir zona, orden y periodo del plan"
flujo: "misas.plan_de_misas_pantalla.gestionar.flujo"
preguntas: ["Como elegir zona, orden y periodo del plan en Elegir zona, orden y periodo del plan?"]
pantallas_principales: ["misas.pantalla.modificar_plan_de_misas", "misas.pantalla.preparar_plan_de_misas", "misas.pantalla.ver_plan_de_misas"]
fragmentos: ["misas.pantalla.modificar_plan_de_misas", "misas.pantalla.preparar_plan_de_misas", "misas.pantalla.ver_plan_de_misas"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
source: "docs/catalogo/misas/flujos/plan_de_misas_pantalla.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir zona, orden y periodo del plan

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir zona, orden y periodo del plan`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir zona, orden y periodo del plan en Elegir zona, orden y periodo del plan?

## Donde Entrar

- Modificar Plan De Misas (`misas.pantalla.modificar_plan_de_misas`)
- Preparar Plan De Misas (`misas.pantalla.preparar_plan_de_misas`)
- Ver Plan De Misas (`misas.pantalla.ver_plan_de_misas`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir zona, orden y periodo del plan

1. Entrar en la pantalla del plan.
2. En **Plan de Misas Zona** elegir la zona.
3. Elegir el **Orden** de los sacerdotes (orden, prioridad o alfabético) y el **Periodo**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/plan_de_misas_pantalla_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_plan_de_misas`
- `misas.pantalla.preparar_plan_de_misas`
- `misas.pantalla.ver_plan_de_misas`
- `misas.pantalla.modificar_plan_de_misas`
- `misas.pantalla.preparar_plan_de_misas`
- `misas.pantalla.ver_plan_de_misas`

## Objetivo

Cargar las zonas, el orden y los tipos de plantilla que ofrecen las pantallas de preparar, modificar y ver el plan de misas.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
