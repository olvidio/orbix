---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "zonassacd"
titulo: "Consultar y reasignar los centros de una zona"
flujo: "zonassacd.zona_ctr.gestionar.flujo"
preguntas: ["Como consultar y reasignar los centros de una zona en Consultar y reasignar los centros de una zona?"]
pantallas_principales: ["zonassacd.pantalla.zona_ctr"]
fragmentos: ["zonassacd.pantalla.zona_ctr", "zonassacd.pantalla.zona_ctr_lista_ajax", "zonassacd.pantalla.zona_ctr_update_ajax"]
endpoints: ["/src/zonassacd/zona_ctr", "/src/zonassacd/zona_ctr_lista", "/src/zonassacd/zona_ctr_update"]
source: "docs/catalogo/zonassacd/flujos/zona_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar y reasignar los centros de una zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar y reasignar los centros de una zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar y reasignar los centros de una zona en Consultar y reasignar los centros de una zona?

## Donde Entrar

- Zona Ctr (`zonassacd.pantalla.zona_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar y reasignar los centros de una zona

1. Abrir «Lista de centros de la zona» y elegir la zona en el desplegable (por ejemplo Pedralbes, o «sin asignar zona» / «sin asignar zona sf»).
2. Aparece la tabla de centros con su zona (las filas sombreadas en azul claro son centros de la sf).
3. Para cambiarlos de zona, marcar los centros, elegir la zona de destino en «asignar los centros marcados a la zona» y pulsar **asignar** (solo si tu rol lo permite).

Referencias tecnicas para verificar la respuesta:
- `/src/zonassacd/zona_ctr`
- `/src/zonassacd/zona_ctr_lista`
- `/src/zonassacd/zona_ctr_update`

## Pantallas Y Fragmentos Relacionados

- `zonassacd.pantalla.zona_ctr`
- `zonassacd.pantalla.zona_ctr`
- `zonassacd.pantalla.zona_ctr_lista_ajax`
- `zonassacd.pantalla.zona_ctr_update_ajax`

## Objetivo

Ver qué centros pertenecen a cada zona de sacerdotes y pasarlos de una zona a otra.

## Errores Documentados

- `hay un error, no se ha guardado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
