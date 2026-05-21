---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Centros"
flujo: "ubis.centros.gestionar.flujo"
preguntas: ["Como crear o modificar en Centros?"]
pantallas_principales: ["ubis.pantalla.centros_que"]
fragmentos: ["ubis.pantalla.centros_form_labor", "ubis.pantalla.centros_form_num", "ubis.pantalla.centros_form_plazas"]
endpoints: ["/src/ubis/centros_update"]
source: "docs/catalogo/ubis/flujos/centros.md"
estado_revision: "generado"
---

# Ayuda IA - Centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Centros?

## Donde Entrar

- Centros Que (`ubis.pantalla.centros_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/centros_update`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.centros_que`
- `ubis.pantalla.centros_form_labor`
- `ubis.pantalla.centros_form_num`
- `ubis.pantalla.centros_form_plazas`

## Objetivo

Gestiona Centros. Actualiza datos de centro DL (labor / num / plazas según POST).

## Errores Documentados

- `Hay un error, no se ha guardado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
