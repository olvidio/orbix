---
id: "actividades.tipo_activ.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Tipo Activ"
entidades: ["TipoActiv", "TipoActivLista"]
acciones: ["crear", "crear_actualizar", "eliminar", "listar"]
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
pantallas: ["frontend/actividades/controller/tipo_activ.php"]
casos_uso: ["src\\actividades\\application\\TipoActivEliminar", "src\\actividades\\application\\TipoActivLista", "src\\actividades\\application\\TipoActivNuevo", "src\\actividades\\application\\TipoActivUpdate"]
tags: ["activ", "actividades", "eliminar", "lista", "nuevo", "tipo", "tipo_activ", "update"]
estado_revision: "generado"
---

# Gestionar Tipo Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ`.

## Objetivo Funcional

Gestiona TipoActiv, TipoActivLista. Actualiza el nombre de un tipo de actividad. Portado del case update del dispatcher legacy. Crea un nuevo tipo de actividad. Portado del case nuevo del dispatcher legacy. Devuelve cadena vacia si todo va bien o un texto de error/aviso. Devuelve la tabla HTML con los tipos de actividad existentes. Portado desde el case lista del dispatcher legacy frontend/actividades/controller/tipo_activ_ajax.php. Elimina un tipo de actividad. Portado del case eliminar del dispatcher legacy.

## Acciones Detectadas

- `crear`
- `crear_actualizar`
- `eliminar`
- `listar`

## Endpoints

- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

## Pantallas Relacionadas

- `frontend/actividades/controller/tipo_activ.php`

## Casos De Uso Detectados

- `src\actividades\application\TipoActivEliminar`
- `src\actividades\application\TipoActivLista`
- `src\actividades\application\TipoActivNuevo`
- `src\actividades\application\TipoActivUpdate`

## Pistas Desde Endpoints

- Actualiza el nombre de un tipo de actividad. Portado del case `update` del dispatcher legacy.
- Crea un nuevo tipo de actividad. Portado del case `nuevo` del dispatcher legacy. Devuelve cadena vacia si todo va bien o un texto de error/aviso.
- Devuelve la tabla HTML con los tipos de actividad existentes. Portado desde el case `lista` del dispatcher legacy frontend/actividades/controller/tipo_activ_ajax.php.
- Elimina un tipo de actividad. Portado del case `eliminar` del dispatcher legacy.

## Errores Conocidos

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
