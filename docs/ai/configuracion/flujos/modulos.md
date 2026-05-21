---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Modulos"
flujo: "configuracion.modulos.gestionar.flujo"
preguntas: ["Como crear o modificar en Modulos?", "Como abrir el formulario en Modulos?"]
pantallas_principales: ["configuracion.pantalla.modulos_update"]
fragmentos: ["configuracion.pantalla.modulos_form"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
source: "docs/catalogo/configuracion/flujos/modulos.md"
estado_revision: "generado"
---

# Ayuda IA - Modulos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modulos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Modulos?
- Como abrir el formulario en Modulos?

## Donde Entrar

- Modulos Update (`configuracion.pantalla.modulos_update`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_update`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/modulos_form_data`

## Pantallas Y Fragmentos Relacionados

- `configuracion.pantalla.modulos_update`
- `configuracion.pantalla.modulos_form`

## Objetivo

Gestiona Modulos, ModulosUpdateAction. Alta / baja / modificación de módulos (respuesta texto plano para AJAX legacy). JSON para {.

## Errores Documentados

- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
