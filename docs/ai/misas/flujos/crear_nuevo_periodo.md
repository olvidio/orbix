---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Preparar un nuevo plan de misas"
flujo: "misas.crear_nuevo_periodo.gestionar.flujo"
preguntas: ["Como preparar un nuevo plan de misas en Preparar un nuevo plan de misas?"]
pantallas_principales: ["misas.pantalla.crear_nuevo_periodo"]
fragmentos: ["misas.pantalla.crear_nuevo_periodo"]
endpoints: ["/src/misas/crear_nuevo_periodo_data"]
source: "docs/catalogo/misas/flujos/crear_nuevo_periodo.md"
estado_revision: "generado"
---

# Ayuda IA - Preparar un nuevo plan de misas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Preparar un nuevo plan de misas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como preparar un nuevo plan de misas en Preparar un nuevo plan de misas?

## Donde Entrar

- Crear Nuevo Periodo (`misas.pantalla.crear_nuevo_periodo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Preparar un nuevo plan de misas

1. Entrar en **Nuevo plan** (Preparar Plan de Misas).
2. Elegir la zona en **Plan de Misas Zona**, el **Tipo de plantilla** (semanal, semanal y domingos o mensual, con una o tres opciones: la plantilla propone 1 o 3 sacerdotes para cada misa, por orden de prioridad; se elige el primero y solo se pasa al segundo si el primero no está o celebra en un centro con más prioridad), el **Orden** y el **Periodo** (próxima semana, próximo mes natural u otro, con fechas).
3. Pulsar **preparar**. Aviso: «Borrará todos los datos introducidos en este periodo!!».
4. Se muestra la cuadrícula del plan, rellenada desde la plantilla, lista para modificar: al pulsar una celda se abre la ventana **Selección de sacd** (ver «Elegir sacerdote en una celda»).

Referencias tecnicas para verificar la respuesta:
- `/src/misas/crear_nuevo_periodo_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.crear_nuevo_periodo`
- `misas.pantalla.crear_nuevo_periodo`

## Objetivo

Crear el plan de misas de un periodo nuevo a partir de la plantilla y mostrarlo en la cuadrícula.

## Errores Documentados

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado en error_txt>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
