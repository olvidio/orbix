---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "zonassacd"
titulo: "Consultar los sacerdotes de una zona"
flujo: "zonassacd.zona_sacd.gestionar.flujo"
preguntas: ["Como consultar los sacerdotes de una zona en Consultar los sacerdotes de una zona?", "Como cambiar los días o la zona de un sacerdote en Consultar los sacerdotes de una zona?"]
pantallas_principales: ["zonassacd.pantalla.zona_sacd"]
fragmentos: ["zonassacd.pantalla.zona_sacd", "zonassacd.pantalla.zona_sacd_lista_ajax", "zonassacd.pantalla.zona_sacd_update_ajax"]
endpoints: ["/src/zonassacd/zona_sacd", "/src/zonassacd/zona_sacd_lista", "/src/zonassacd/zona_sacd_update"]
source: "docs/catalogo/zonassacd/flujos/zona_sacd.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los sacerdotes de una zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los sacerdotes de una zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los sacerdotes de una zona en Consultar los sacerdotes de una zona?
- Como cambiar los días o la zona de un sacerdote en Consultar los sacerdotes de una zona?

## Donde Entrar

- Zona Sacd (`zonassacd.pantalla.zona_sacd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los sacerdotes de una zona

1. Abrir «Lista de sacd de la zona» y elegir la zona (o «sin asignar zona»).
2. Aparece una tabla con sacerdote, zona, **propia** (1 si es su zona propia) y los días **L M X J V S D** (una «x» o un «-» en cada día: indica si el sacerdote atiende o no ese día, es decir, si está o no está).

Referencias tecnicas para verificar la respuesta:
- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_lista`
- `/src/zonassacd/zona_sacd_update`

## Cambiar los días o la zona de un sacerdote

1. Marcar al sacerdote y pulsar **modificar**: se abre una ventana con su nombre, la casilla **propia** y los días (Lunes a Domingo). Pulsar **Grabar** para guardar o **Cancel** para salir.
2. Para cambiar de zona a varios sacerdotes: marcarlos, elegir la zona en «asignar los sacd marcados a la zona» y pulsar **cambiar asignación zona**; con **añadir asignación iglesia/cgi** en lugar de cambiar de zona se añade otra, de modo que el sacerdote queda con dos zonas.

Referencias tecnicas para verificar la respuesta:
- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_lista`
- `/src/zonassacd/zona_sacd_update`

## Pantallas Y Fragmentos Relacionados

- `zonassacd.pantalla.zona_sacd`
- `zonassacd.pantalla.zona_sacd`
- `zonassacd.pantalla.zona_sacd_lista_ajax`
- `zonassacd.pantalla.zona_sacd_update_ajax`

## Objetivo

Ver los sacerdotes de cada zona, los días de la semana que atienden, y cambiarlos de zona.

## Errores Documentados

- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
