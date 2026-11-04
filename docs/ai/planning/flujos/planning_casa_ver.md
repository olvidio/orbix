---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "planning"
titulo: "Ver el calendario de casas"
flujo: "planning.planning_casa_ver.gestionar.flujo"
preguntas: ["Como ver el calendario de casas en Ver el calendario de casas?"]
pantallas_principales: ["planning.pantalla.planning_casa_que", "planning.pantalla.planning_casa_select"]
fragmentos: ["planning.pantalla.planning_casa_ver"]
endpoints: ["/src/planning/planning_casa_ver_data"]
source: "docs/catalogo/planning/flujos/planning_casa_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el calendario de casas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el calendario de casas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el calendario de casas en Ver el calendario de casas?

## Donde Entrar

- Planning por casas (filtros) (`planning.pantalla.planning_casa_que`)
- Selección de casas (planning) (`planning.pantalla.planning_casa_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el calendario de casas

1. Aparece «Calendario de casas» con los meses del periodo en columnas y una fila por casa; los domingos van marcados.
2. Cada actividad es una barra con su tipo abreviado (por ejemplo «sg crt», «n ca»); el color indica el tipo y si está en proyecto o provisional. El botón **Ver leyenda** abre una ventana con los colores: actividad sv, sf, otras, propia, personal, provisional (si la plaza está pedida, en espera o denegada) y en proyecto.
3. **Descargar html** guarda el calendario como página web.
4. Desde la entrada de menú de **nuevo planning** del calendario de propuestas, la tabla permite crear una actividad nueva en una casa o modificar una existente.

Referencias tecnicas para verificar la respuesta:
- `/src/planning/planning_casa_ver_data`

## Pantallas Y Fragmentos Relacionados

- `planning.pantalla.planning_casa_que`
- `planning.pantalla.planning_casa_select`
- `planning.pantalla.planning_casa_ver`

## Objetivo

Ver el calendario de ocupación de las casas elegidas: qué actividad hay en cada casa y cada día.

## Errores Documentados

- `Faltan fechas de periodo (f_ini_iso / f_fin_iso).`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
