---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Consultar los módulos definidos"
flujo: "configuracion.modulos_select.gestionar.flujo"
preguntas: ["Como consultar los módulos definidos en Consultar los módulos definidos?", "Como añadir un módulo en Consultar los módulos definidos?", "Como modificar un módulo en Consultar los módulos definidos?", "Como eliminar un módulo en Consultar los módulos definidos?"]
pantallas_principales: ["configuracion.pantalla.modulos_select"]
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
endpoints: ["/src/configuracion/modulos_select_data", "/src/configuracion/modulos_update"]
source: "docs/catalogo/configuracion/flujos/modulos_select.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar los módulos definidos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar los módulos definidos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar los módulos definidos en Consultar los módulos definidos?
- Como añadir un módulo en Consultar los módulos definidos?
- Como modificar un módulo en Consultar los módulos definidos?
- Como eliminar un módulo en Consultar los módulos definidos?

## Donde Entrar

- Definir módulos (`configuracion.pantalla.modulos_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar los módulos definidos

1. Abrir **definir módulos**: aparece la tabla de módulos con sus dependencias.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

## Añadir un módulo

1. Pulsar **añadir módulo** y seguir los pasos de «Añadir o modificar un módulo».

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

## Modificar un módulo

1. Seleccionar una sola fila.
2. Pulsar **modificar**.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

## Eliminar un módulo

1. Seleccionar la fila y pulsar el botón de eliminar.
2. Confirmar.
3. La lista se refresca; si no se puede borrar, sale un aviso con el motivo.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

## Pantallas Y Fragmentos Relacionados

- `configuracion.pantalla.modulos_select`
- `configuracion.pantalla.modulos_form`
- `configuracion.pantalla.modulos_update`

## Objetivo

Ver los módulos definidos en el esquema y acceder al alta, la modificación o la baja de cada uno.

## Errores Documentados

- `hay un error, no se ha eliminado (+ texto de getErrorTxt() del repositorio)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
