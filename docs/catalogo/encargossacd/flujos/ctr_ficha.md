---
id: "encargossacd.ctr_ficha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Ctr Ficha"
capacidad: "encargossacd.ctr_ficha.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.ctr_ficha", "encargossacd.pantalla.ctr_ficha_update"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/encargossacd/ctr_ficha_data", "/src/encargossacd/ctr_ficha_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ctr Ficha

Propuesta generada automaticamente desde la capacidad `encargossacd.ctr_ficha.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CtrFicha. Datos de la pantalla ctr_ficha: - calcula el filtro_ctr efectivo a partir del centro (cuando no viene del POST) - devuelve las opciones_seccion para el desplegable de grupo de ctrs. Reemplaza la lectura directa de repos y el acceso a EncargoAplicacionService que el frontend hacia en ctr_ficha.php. Mutacion de la ficha de atencion sacerdotal de un centro. Puerto de frontend/encargossacd/controller/ctr_ficha_update.php. Devuelve siempre ['error' => string] (vacio = exito). El controlador HTTP convierte ese resultado en JSON {success, mensaje} (el proxy legacy en frontend/ preserva el contrato "alert(rta_txt)" reemitiendo mensaje).

## Punto De Entrada

Menú: dre > Encargos > ficha ctr.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.ctr_ficha_update`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/encargossacd/ctr_ficha_update`

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.filtro_ctr`
- `form.id_ubi`
- `post.filtro_ctr`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_lista_ctrs`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/ctr_ficha_data`
- `/src/encargossacd/ctr_ficha_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr


## Ruta de menú

- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr

