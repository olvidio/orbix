---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Ver el planning de las personas de un centro"
flujo: "planning.planning_ctr_select.gestionar.flujo"
preguntas: ["Como ver el planning de las personas de un centro en Ver el planning de las personas de un centro?"]
pantallas_principales: ["planning.pantalla.planning_ctr_que"]
fragmentos: ["planning.pantalla.planning_ctr_select"]
endpoints: ["/src/planning/planning_ctr_select_data"]
source: "docs/catalogo/planning/flujos/planning_ctr_select.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el planning de las personas de un centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el planning de las personas de un centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el planning de las personas de un centro en Ver el planning de las personas de un centro?

## Donde Entrar

- Planning por centro (filtros) (`planning.pantalla.planning_ctr_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el planning de las personas de un centro

1. Abrir «ACTIVIDADES DE LAS PERSONAS DE UN CENTRO».
2. Escribir el **Centro** (si se deja vacío salen todos los n ordenados por centro) o marcar **todos los ctr con n**, **todos los ctr con agd** o **todos los ctr con s**.
3. Elegir **incluir sacd**: no / sí.
4. En **PERIODO DEL PLANNING ACTIVIDADES** elegir todo el año, un trimestre u **otro** (con fecha de inicio y de fin) y el año.
5. Pulsar **Planning vista tabla** o **vista para imprimir**.
6. Si un centro no existe o no tiene personas, avisa «No encuentro este ctr» o «No encuentro personas para …». El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.

Referencias tecnicas para verificar la respuesta:
- `/src/planning/planning_ctr_select_data`

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_ctr_que`
- `planning.pantalla.planning_ctr_select`

## Objetivo

Ver el calendario de actividades de las personas de un centro (o de todos los centros de un colectivo).

## Errores Documentados

- `Faltan fechas de periodo`
- `No encuentro este ctr`
- `No encuentro personas para %s`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
