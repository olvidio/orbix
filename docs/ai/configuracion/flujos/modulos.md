---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Añadir o modificar un módulo"
flujo: "configuracion.modulos.gestionar.flujo"
preguntas: ["Como añadir o modificar un módulo en Añadir o modificar un módulo?", "Como eliminar un módulo en Añadir o modificar un módulo?"]
pantallas_principales: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
source: "docs/catalogo/configuracion/flujos/modulos.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir o modificar un módulo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir o modificar un módulo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir o modificar un módulo en Añadir o modificar un módulo?
- Como eliminar un módulo en Añadir o modificar un módulo?

## Donde Entrar

- Ficha de módulo (`configuracion.pantalla.modulos_form`)
- Proxy AJAX modulos_update (`configuracion.pantalla.modulos_update`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir o modificar un módulo

1. En la lista de módulos, pulsar **añadir módulo**, o seleccionar uno y pulsar **modificar**.
2. Aparece el formulario con los módulos y aplicaciones disponibles. Las aplicaciones que necesitan otros módulos salen marcadas y no se pueden desmarcar.
3. Rellenar o cambiar el nombre, la descripción y las dependencias (al marcar o desmarcar una casilla, el cambio se guarda al momento).
4. Pulsar **Guardar cambios** para volver a la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_form_data`
- `/src/configuracion/modulos_update`

## Eliminar un módulo

1. Se hace desde la lista de módulos, no desde el formulario (ver «Consultar los módulos definidos»).

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_form_data`
- `/src/configuracion/modulos_update`

## Pantallas Y Fragmentos Relacionados

- `configuracion.pantalla.modulos_form`
- `configuracion.pantalla.modulos_update`
- `configuracion.pantalla.modulos_form`
- `configuracion.pantalla.modulos_update`

## Objetivo

Dar de alta un módulo nuevo, o cambiar el nombre, la descripción y las dependencias (módulos y aplicaciones que necesita) de uno existente.

## Errores Documentados

- `hay un error, no se ha eliminado (solo en baja desde listado)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
