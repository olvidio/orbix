---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver y modificar el plan de una zona"
flujo: "misas.ver_cuadricula_zona.gestionar.flujo"
preguntas: ["Como ver y modificar el plan de una zona en Ver y modificar el plan de una zona?"]
pantallas_principales: ["misas.pantalla.ver_cuadricula_zona"]
fragmentos: ["misas.pantalla.modificar_cuadricula_zona", "misas.pantalla.ver_cuadricula_zona"]
endpoints: ["/src/misas/ver_cuadricula_zona_data"]
source: "docs/catalogo/misas/flujos/ver_cuadricula_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Ver y modificar el plan de una zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver y modificar el plan de una zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver y modificar el plan de una zona en Ver y modificar el plan de una zona?

## Donde Entrar

- Ver Cuadricula Zona (`misas.pantalla.ver_cuadricula_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver y modificar el plan de una zona

1. Entrar en **Modificar plan** (o **Ver plan zona** para solo consulta).
2. Elegir la zona, el **Orden** y el **Periodo** (esta semana, este mes, próxima semana, próximo mes u otro) y pulsar buscar.
3. Aparece la cuadrícula con las misas del periodo.
4. Al elegir la zona en **Nuevo plan** o **Modificar plan** ya aparece la cuadrícula del periodo (una fila por sacerdote, una columna por día; se ven SI, NO o -- según el día). En modificar, pulsar una celda para elegir sacerdote; si no hay datos aparece «No hay datos para mostrar en la cuadrícula».

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_cuadricula_zona_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_cuadricula_zona`
- `misas.pantalla.modificar_cuadricula_zona`
- `misas.pantalla.ver_cuadricula_zona`

## Objetivo

Mostrar la cuadrícula del plan de misas de una zona (encargos en filas, días en columnas, sacerdotes en las celdas) para consultarla o modificarla.

## Errores Documentados

- `hay un error, no se ha guardado`
- `sólo debería haber uno`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
