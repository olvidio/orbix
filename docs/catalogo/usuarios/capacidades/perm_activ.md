---
id: "usuarios.perm_activ.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Perm Activ"
entidades: ["PermActiv"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/perm_activ_eliminar", "/src/usuarios/perm_activ_guardar", "/src/usuarios/perm_activ_lista"]
pantallas: ["frontend/procesos/controller/usuario_perm_activ.php", "frontend/usuarios/controller/perm_activ_lista.php"]
casos_uso: []
tags: ["activ", "eliminar", "guardar", "lista", "perm", "perm_activ", "usuarios"]
estado_revision: "generado"
---

# Gestionar Perm Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `perm_activ`.

## Objetivo Funcional

Gestiona PermActiv. Descripcion funcional pendiente de revisar. Para la tabla slickGrid, el width debe ser en pixels No hay que poner unidades, pues da un error de javascript.

## Acciones Detectadas

- `eliminar`
- `guardar`
- `listar`

## Endpoints

- `/src/usuarios/perm_activ_eliminar`
- `/src/usuarios/perm_activ_guardar`
- `/src/usuarios/perm_activ_lista`

## Pantallas Relacionadas

- `frontend/procesos/controller/usuario_perm_activ.php`
- `frontend/usuarios/controller/perm_activ_lista.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.
- Para la tabla slickGrid, el width debe ser en pixels No hay que poner unidades, pues da un error de javascript.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
