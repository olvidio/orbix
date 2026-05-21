---
id: "misas.modificar_encargos_centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Modificar Encargos Centros"
capacidad: "misas.modificar_encargos_centros.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_encargos_centros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Modificar Encargos Centros

Propuesta generada automaticamente desde la capacidad `misas.modificar_encargos_centros.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ModificarEncargosCentros. Devuelve el desplegable de zonas que el usuario actual puede ver, para pintar la pantalla modificar_encargos_centros. Replica la logica de permisos de apps/misas/controller/modificar_encargos_centros.php: si el rol es p-sacd y NO es jefe de calendario, se limitan las zonas a las del id_pau del propio usuario. Devuelve: - error : texto vacio si todo ok, mensaje si falta permiso. - a_opciones_zona: array id_zona => nombre_zona.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_encargos_centros`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_zona`

Acciones JavaScript:
- `fnjs_ver_encargos_centros`

## Endpoints Del Flujo

- `/src/misas/modificar_encargos_centros_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
