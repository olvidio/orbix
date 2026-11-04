---
id: "configuracion.parametros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Ver los parámetros del esquema"
capacidad: "configuracion.parametros.gestionar"
pantallas_principales: ["configuracion.pantalla.parametros"]
fragmentos: []
acciones: ["listar", "crear_actualizar"]
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
estado_revision: "revisado"
---

# Flujo - Configurar parámetros del esquema

## Objetivo De Usuario

Consultar y cambiar los parámetros generales del esquema: curso escolar, certificados, idioma, ámbito territorial, gestión de calendario, etc.

## Punto De Entrada

Menú **sistema > Configuración > config esquema** (Pills2: ADMIN LOCAL > Esquema).

## Escenarios Inferidos

### Ver los parámetros del esquema

Pasos propuestos:
1. Abrir **config esquema**: aparece un bloque por cada parámetro con su valor actual.

Endpoints asociados:
- `/src/configuracion/parametros_lista`
- `/src/configuracion/parametros_update`

### Cambiar un parámetro

Pasos propuestos:
1. Editar el bloque que interese.
2. Los periodos del stgr y del crt se escriben como día y mes de inicio y de fin; el resto de parámetros llevan un solo valor (texto, opción o desplegable).
3. Pulsar **Guardar**: sale el aviso «se ha guardado correctamente».

Endpoints asociados:
- `/src/configuracion/parametros_lista`
- `/src/configuracion/parametros_update`

## Errores Conocidos

Sin mensajes `_()` documentados en el endpoint; fallos de carga inicial se muestran en pantalla.

## Ruta de menú

- **Legacy:** sistema > Configuración > config esquema
- **Pills2:** ADMIN LOCAL > Esquema; sistema > Configuración > config esquema
