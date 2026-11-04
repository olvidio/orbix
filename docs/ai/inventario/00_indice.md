---
tipo: "ayuda_ia"
subtipo: "indice"
modulo: "inventario"
flujos: 44
pantallas: 34
endpoints: 43
estado_revision: "generado"
---

# Ayuda IA - inventario

Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.

## Como Debe Responder La IA

- Priorizar pasos de usuario sobre detalles tecnicos.
- Si falta ruta de menu, decir que esta pendiente de documentar.
- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.
- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.

## Flujos Disponibles

- Editar los textos tipo -> `flujos/cabecera_pie_txt.md`
- Guardar la asignación a centros -> `flujos/doc_asignar_ctr.md`
- Guardar la asignación a la dlb/casa -> `flujos/doc_asignar_dlb.md`
- Modificar varios documentos a la vez -> `flujos/documentos.md`
- Ciclo completo de un equipaje -> `flujos/equipajes.md`
- Añadir un documento a una valija -> `flujos/equipajes_add_doc.md`
- Quitar un documento de una valija -> `flujos/equipajes_del_doc.md`
- Consultar los documentos de la casa y sus valijas -> `flujos/equipajes_doc_casa.md`
- Imprimir un equipaje -> `flujos/equipajes_egm.md`
- Eliminar una valija -> `flujos/equipajes_eliminar_grupo.md`
- Consultar las actividades de un equipaje -> `flujos/equipajes_lista_activ_equipaje.md`
- Elegir las actividades del equipaje -> `flujos/equipajes_lista_activ_periodo.md`
- Nombrar el equipaje -> `flujos/equipajes_lista_activ_sel.md`
- Consultar los movimientos de un equipaje -> `flujos/equipajes_movimientos.md`
- Crear un equipaje nuevo -> `flujos/equipajes_nuevo.md`
- Cambiar los textos del listado del equipaje -> `flujos/equipajes_texto_listado.md`
- Crear una valija con documentos -> `flujos/equipajes_update_grupo.md`
- Qué hace al imprimir -> `flujos/inventario_css_inline.md`
- Imprimir el inventario de centros -> `flujos/inventario_ctr.md`
- Imprimir el inventario de la dlb/casa -> `flujos/inventario_dlb.md`
- Elegir inventario de centros o de dlb/casa -> `flujos/inventario_que.md`
- Consultar las casas del periodo -> `flujos/lista_casas_posibles_periodo.md`
- Agrupar un listado por colección -> `flujos/lista_colecciones.md`
- Elegir el centro de origen y de destino -> `flujos/lista_de_ctr.md`
- Elegir centros para asignar un documento -> `flujos/lista_de_ctr_con_docs.md`
- Consultar los centros que tienen el documento -> `flujos/lista_docs_asignados_por_tipo.md`
- Rellenar la asignación a centros -> `flujos/lista_docs_asignar_ctr.md`
- Rellenar la asignación a la dlb/casa -> `flujos/lista_docs_asignar_dlb.md`
- Consultar los documentos con observaciones -> `flujos/lista_docs_con_observaciones.md`
- Buscar los documentos que se van a trasladar -> `flujos/lista_docs_de_ctr.md`
- Elegir lugares para asignar un documento -> `flujos/lista_docs_de_dlb.md`
- Ver los documentos de una valija -> `flujos/lista_docs_de_egm.md`
- Ver los documentos de la casa -> `flujos/lista_docs_de_lugar.md`
- Consultar los documentos pendientes -> `flujos/lista_docs_en_busqueda.md`
- Elegir un documento libre -> `flujos/lista_docs_libres.md`
- Consultar los centros que no tienen el documento -> `flujos/lista_docs_no_asignados_por_tipo.md`
- Consultar los documentos perdidos -> `flujos/lista_docs_perdidos.md`
- Cambiar los equipajes que se ven -> `flujos/lista_equipajes_desde_fecha.md`
- Crear una valija nueva -> `flujos/lista_equipajes_posibles_maletas.md`
- Elegir el lugar dentro del centro -> `flujos/lista_lugares_de_ubi.md`
- Elegir el tipo de documento -> `flujos/lista_tipo_doc.md`
- Consultar y mantener las tablas del inventario -> `flujos/tablas_inventario.md`
- Abrir el texto para modificarlo -> `flujos/texto_de_egm.md`
- Trasladar documentos a otro centro -> `flujos/traslado_doc.md`

## Pantallas Disponibles

- Textos cabecera/pie equipajes -> `pantallas/cabecera_pie_txt.md`
- Documentos ya asignados -> `pantallas/doc_asignado.md`
- Formulario asignar a centros -> `pantallas/doc_asignar_ctr.md`
- Formulario asignar DLB -> `pantallas/doc_asignar_dlb.md`
- Inventario por centros -> `pantallas/doc_de_ctr.md`
- Inventario DLB/casa -> `pantallas/doc_de_dlb.md`
- Imprimir inventario centros -> `pantallas/doc_imprimir_ctr.md`
- Imprimir inventario DLB -> `pantallas/doc_imprimir_dlb.md`
- Centros sin documento -> `pantallas/doc_no_asignado.md`
- Asignar documentos — selector -> `pantallas/docs_asignar_que.md`
- Documentos con observaciones -> `pantallas/docs_con_observaciones.md`
- Documentos pendientes -> `pantallas/docs_en_busqueda.md`
- Documentos perdidos -> `pantallas/docs_perdidos.md`
- Edición masiva documentos -> `pantallas/documentos_form.md`
- Casas posibles -> `pantallas/equipajes_casas_posibles.md`
- Desplegable equipajes -> `pantallas/equipajes_desplegable.md`
- Docs por casa (equipaje) -> `pantallas/equipajes_doc_casa.md`
- Docs libres -> `pantallas/equipajes_docs_libres.md`
- Añadir doc a maleta -> `pantallas/equipajes_form_add.md`
- Quitar doc de maleta -> `pantallas/equipajes_form_del.md`
- Fragmento selección actividades -> `pantallas/equipajes_form_nuevo.md`
- Editar texto listado -> `pantallas/equipajes_form_texto_listado.md`
- Imprimir equipaje -> `pantallas/equipajes_imprimir.md`
- Actividades por periodo -> `pantallas/equipajes_lista_activ_periodo.md`
- Lista docs EGM/lugar -> `pantallas/equipajes_lista_docs.md`
- Movimientos maletas — resultado -> `pantallas/equipajes_movimientos.md`
- Movimientos maletas — filtro -> `pantallas/equipajes_movimientos_que.md`
- Nuevo equipaje -> `pantallas/equipajes_nuevo.md`
- Elegir maleta/grupo -> `pantallas/equipajes_posibles_maletas.md`
- Gestionar equipajes -> `pantallas/equipajes_ver.md`
- Ver docs de lugar -> `pantallas/equipajes_ver_docs.md`
- Elegir inventario centros o DLB -> `pantallas/inventario_que.md`
- Traslado — lista y guardar -> `pantallas/traslado_doc_lista.md`
- Traslado de documentos — filtro -> `pantallas/traslado_doc_que.md`
