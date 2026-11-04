---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Modificar la plantilla de una zona"
flujo: "misas.modificar_plantilla.gestionar.flujo"
preguntas: ["Como modificar la plantilla de una zona en Modificar la plantilla de una zona?", "Como importar de otra plantilla en Modificar la plantilla de una zona?"]
pantallas_principales: ["misas.pantalla.modificar_plantilla"]
fragmentos: ["misas.pantalla.modificar_plantilla"]
endpoints: ["/src/misas/modificar_plantilla_data"]
source: "docs/catalogo/misas/flujos/modificar_plantilla.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar la plantilla de una zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar la plantilla de una zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar la plantilla de una zona en Modificar la plantilla de una zona?
- Como importar de otra plantilla en Modificar la plantilla de una zona?

## Donde Entrar

- Modificar Plantilla (`misas.pantalla.modificar_plantilla`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar la plantilla de una zona

1. Entrar en **Modificar plantilla**.
2. Elegir la zona en **Plantilla de la zona**, el **Tipo de plantilla** y el **Orden**.
3. Editar la cuadrícula que aparece.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/modificar_plantilla_data`

## Importar de otra plantilla

1. En el desplegable **Importar de la plantilla** elegir el tipo de plantilla origen.
2. Pulsar **importar**. Aviso: «Borrará todos los datos introducidos en la plantilla que ves!!».

Referencias tecnicas para verificar la respuesta:
- `/src/misas/modificar_plantilla_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.modificar_plantilla`
- `misas.pantalla.modificar_plantilla`

## Objetivo

Editar la plantilla de una zona: las tareas que se repiten cada semana o mes y quién las hace.

## Errores Documentados

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
