---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Cartas Presentacion Shell"
flujo: "cartaspresentacion.cartas_presentacion_shell.gestionar.flujo"
preguntas: []
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: []
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data", "/src/cartaspresentacion/ubis_lista_data", "/src/cartaspresentacion/poblaciones_data", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update", "/src/cartaspresentacion/carta_presentacion_eliminar"]
source: "docs/catalogo/cartaspresentacion/flujos/cartas_presentacion_shell.md"
estado_revision: "generado"
---

# Ayuda IA - Cartas Presentacion Shell

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cartas Presentacion Shell`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Cartas Presentacion (`cartaspresentacion.pantalla.cartas_presentacion`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion`

## Objetivo

Mantener los datos de presentación (director, contacto, zona) de los centros de la delegación o de regiones extranjeras.

## Errores Documentados

- `Formulario: No puede modificar datos de otra dl, Centro no encontrado.`
- `Update: Hay un error, no se ha guardado.`
- `Eliminar: Carta de presentacion no encontrada, Hay un error, no se ha borrado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
