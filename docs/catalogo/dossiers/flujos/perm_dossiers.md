---
id: "dossiers.perm_dossiers.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Perm Dossiers"
capacidad: "dossiers.perm_dossiers.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.perm_dossiers"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/perm_dossiers_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Perm Dossiers

Propuesta generada automaticamente desde la capacidad `dossiers.perm_dossiers.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PermDossiers. Listado de tipos de dossier para pantalla de permisos. pagina_link_spec se firma en perm_dossiers_data.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.perm_dossiers`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.tipo`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/dossiers/perm_dossiers_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
