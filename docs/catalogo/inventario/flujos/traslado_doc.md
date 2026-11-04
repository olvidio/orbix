---
id: "inventario.traslado_doc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Trasladar documentos a otro centro"
capacidad: "inventario.traslado_doc.gestionar"
pantallas_principales: ["inventario.pantalla.traslado_doc_que"]
fragmentos: []
acciones: ["guardar"]
endpoints: ["/src/inventario/traslado_doc_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Traslado Doc

## Objetivo De Usuario

Pasar documentos de un centro (y lugar) a otro.

## Punto De Entrada

Menú **Inventario > inventarios > traslado de doc**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Trasladar documentos a otro centro

Pasos propuestos:
1. Abrir **traslado de doc**.
2. Elegir el centro de origen, luego el lugar, y pulsar **Buscar**. La lista puede ser muy larga (más de mil documentos en un almacén).
3. Marcar en **lista de documentos del centro** los documentos que se mueven.
4. Elegir el centro y el lugar de destino.
5. Pulsar **Asignar**: los documentos pasan al lugar de destino y la lista de origen se actualiza.
6. Si hay un problema aparece un aviso **respuesta**.

Endpoints asociados:
- `/src/inventario/traslado_doc_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/traslado_doc_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** —
