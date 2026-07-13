---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Definir módulos (listado)"
flujo: "configuracion.modulos_select.gestionar.flujo"
preguntas: []
pantallas_principales: ["configuracion.pantalla.modulos_select"]
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
endpoints: ["/src/configuracion/modulos_select_data", "/src/configuracion/modulos_update"]
source: "docs/catalogo/configuracion/flujos/modulos_select.md"
estado_revision: "generado"
---

# Ayuda IA - Definir módulos (listado)

Usa este documento para responder preguntas de usuario sobre como trabajar con `Definir módulos (listado)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Definir módulos (`configuracion.pantalla.modulos_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `configuracion.pantalla.modulos_select`
- `configuracion.pantalla.modulos_form`
- `configuracion.pantalla.modulos_update`

## Objetivo

Consultar los módulos definidos en el esquema y acceder a alta, edición o baja de cada uno.

## Errores Documentados

- `hay un error, no se ha eliminado (+ texto de getErrorTxt() del repositorio)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
