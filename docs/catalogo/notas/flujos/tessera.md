---
id: "notas.tessera.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Tessera"
capacidad: "notas.tessera.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.tessera_copiar_select"]
acciones: ["copiar"]
endpoints: ["/src/notas/tessera_copiar"]
estado_revision: "generado"
---

# Flujo - Gestionar Tessera

Propuesta generada automaticamente desde la capacidad `notas.tessera.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Tessera. Copia todas las PersonaNota de una persona origen hacia una persona destino. Utilizado por personas_select.phtml (pagina de traslado de tessera entre numerarios / supernumerarios). Devuelve una cadena con los errores (separados por <br>) o vacia si todo ha ido bien.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.tessera_copiar_select`

## Escenarios Inferidos

### Copiar

Pasos propuestos:
1. Abrir el listado en el contexto origen/destino correspondiente.
2. Pulsar la accion de copiar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que los datos copiados aparecen en el listado.

Endpoints asociados:
- `/src/notas/tessera_copiar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_nom_dst`
- `html.copiar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_copiar`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/tessera_copiar`

## Errores Conocidos

- ``No se han recibido las personas de origen y destino``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
