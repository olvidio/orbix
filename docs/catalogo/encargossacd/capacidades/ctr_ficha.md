---
id: "encargossacd.ctr_ficha.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Ctr Ficha"
entidades: ["CtrFicha"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/encargossacd/ctr_ficha_data", "/src/encargossacd/ctr_ficha_update"]
pantallas: ["frontend/encargossacd/controller/ctr_ficha.php", "frontend/encargossacd/controller/ctr_ficha_update.php"]
casos_uso: ["src\\encargossacd\\application\\CtrFichaData", "src\\encargossacd\\application\\CtrFichaUpdate"]
tags: ["ctr", "ctr_ficha", "data", "encargossacd", "ficha", "update"]
estado_revision: "generado"
---

# Gestionar Ctr Ficha

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ctr_ficha`.

## Objetivo Funcional

Gestiona CtrFicha. Datos de la pantalla ctr_ficha: - calcula el filtro_ctr efectivo a partir del centro (cuando no viene del POST) - devuelve las opciones_seccion para el desplegable de grupo de ctrs. Reemplaza la lectura directa de repos y el acceso a EncargoAplicacionService que el frontend hacia en ctr_ficha.php. Mutacion de la ficha de atencion sacerdotal de un centro. Puerto de frontend/encargossacd/controller/ctr_ficha_update.php. Devuelve siempre ['error' => string] (vacio = exito). El controlador HTTP convierte ese resultado en JSON {success, mensaje} (el proxy legacy en frontend/ preserva el contrato "alert(rta_txt)" reemitiendo mensaje).

## Acciones Detectadas

- `crear_actualizar`
- `obtener_datos`

## Endpoints

- `/src/encargossacd/ctr_ficha_data`
- `/src/encargossacd/ctr_ficha_update`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/ctr_ficha.php`
- `frontend/encargossacd/controller/ctr_ficha_update.php`

## Casos De Uso Detectados

- `src\encargossacd\application\CtrFichaData`
- `src\encargossacd\application\CtrFichaUpdate`

## Pistas Desde Endpoints

- Datos de la pantalla `ctr_ficha`: - calcula el `filtro_ctr` efectivo a partir del centro (cuando no viene del POST) - devuelve las `opciones_seccion` para el desplegable de grupo de ctrs. Reemplaza la lectura directa de repos y el acceso a `EncargoAplicacionService` que el frontend hacia en `ctr_ficha.php`.
- Mutacion de la ficha de atencion sacerdotal de un centro. Puerto de `frontend/encargossacd/controller/ctr_ficha_update.php`. Devuelve siempre `['error' => string]` (vacio = exito). El controlador HTTP convierte ese resultado en JSON `{success, mensaje}` (el proxy legacy en `frontend/` preserva el contrato "alert(rta_txt)" reemitiendo `mensaje`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
