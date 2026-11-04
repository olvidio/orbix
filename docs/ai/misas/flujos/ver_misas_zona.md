---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Consultar el plan de misas de una zona"
flujo: "misas.ver_misas_zona.gestionar.flujo"
preguntas: ["Como consultar el plan de misas de una zona en Consultar el plan de misas de una zona?"]
pantallas_principales: ["misas.pantalla.ver_misas_zona"]
fragmentos: ["misas.pantalla.ver_misas_zona"]
endpoints: ["/src/misas/ver_misas_zona_data"]
source: "docs/catalogo/misas/flujos/ver_misas_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar el plan de misas de una zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar el plan de misas de una zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el plan de misas de una zona en Consultar el plan de misas de una zona?

## Donde Entrar

- Ver Misas Zona (`misas.pantalla.ver_misas_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el plan de misas de una zona

1. Entrar en **Ver plan zona**.
2. Elegir la zona en **Plan de Misas de la zona**, el **Orden** y el **Periodo** (o fechas concretas con «otro»).
3. Se muestra la cuadrícula de consulta; no se puede cambiar nada.
4. Leyenda de la fila de cada sacerdote (por día): **SI** = el sacerdote está en la zona ese día; **NO** = no está en la zona ese día; **--** = ese día está en otro sitio (al pasar el ratón sale «Está en …»). El fondo rojo avisa de un problema: «Tiene dos Misas a primera hora» o «No está en la zona y tiene Misa a primera hora».

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_misas_zona_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_misas_zona`
- `misas.pantalla.ver_misas_zona`

## Objetivo

Consultar las misas de una zona en un rango de fechas, sin poder modificarlas.

## Errores Documentados

- `solo deberia haber uno`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
