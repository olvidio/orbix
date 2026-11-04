---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Editar una celda del plan de misas"
flujo: "misas.cuadricula.gestionar.flujo"
preguntas: ["Como editar una celda del plan de misas en Editar una celda del plan de misas?"]
pantallas_principales: ["modificar_plan_de_misas", "preparar_plan_de_misas", "modificar_plantilla"]
fragmentos: ["ver_cuadricula_zona", "modificar_cuadricula_zona", "crear_nuevo_periodo", "importar_plantilla"]
endpoints: [""]
source: "docs/catalogo/misas/flujos/cuadricula.md"
estado_revision: "generado"
---

# Ayuda IA - Editar una celda del plan de misas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Editar una celda del plan de misas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como editar una celda del plan de misas en Editar una celda del plan de misas?

## Donde Entrar

- modificar_plan_de_misas (`modificar_plan_de_misas`)
- preparar_plan_de_misas (`preparar_plan_de_misas`)
- modificar_plantilla (`modificar_plantilla`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Editar una celda del plan de misas

1. Entrar en **Modificar plan** y buscar la zona y el periodo.
2. Pulsar la celda del encargo y día; elegir el sacerdote (o dejarla vacía para borrar), con horario y observaciones si procede.
3. Para periodos nuevos usar **Nuevo plan**; para copiar plantillas, **Modificar plantilla > importar**.
4. Leyenda de la fila de cada sacerdote (por día): **SI** = el sacerdote está en la zona ese día; **NO** = no está en la zona ese día; **--** = ese día está en otro sitio (al pasar el ratón sale «Está en …»). El fondo rojo avisa de un problema: «Tiene dos Misas a primera hora» o «No está en la zona y tiene Misa a primera hora».

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_cuadricula_zona_data`
- `/src/misas/cuadricula_update`
- `/src/misas/desplegable_sacd`
- `/src/misas/crear_nuevo_periodo_data`
- `/src/misas/importar_plantilla_data`

## Pantallas Y Fragmentos Relacionados

- `modificar_plan_de_misas`
- `preparar_plan_de_misas`
- `modificar_plantilla`
- `ver_cuadricula_zona`
- `modificar_cuadricula_zona`
- `crear_nuevo_periodo`
- `importar_plantilla`

## Objetivo

Asignar, cambiar o borrar el sacerdote de una celda del plan de misas; preparar un periodo nuevo o copiar una plantilla a otra.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
