-- Vista av_actividades por esquema comun (UNION actividades dl + importadas publicadas).
CREATE OR REPLACE VIEW *.av_actividades AS
SELECT id_activ, id_tipo_activ, dl_org, nom_activ, id_ubi, desc_activ, f_ini, h_ini, f_fin, h_fin, tipo_horario, precio, num_asistentes, status, observ, nivel_stgr, observ_material, lugar_esp, tarifa, id_repeticion, publicado, id_tabla, plazas, idioma FROM *.a_actividades_dl
UNION
SELECT id_activ, id_tipo_activ, dl_org, nom_activ, id_ubi, desc_activ, f_ini, h_ini, f_fin, h_fin, tipo_horario, precio, num_asistentes, status, observ, nivel_stgr, observ_material, lugar_esp, tarifa, id_repeticion, publicado, id_tabla, plazas, idioma FROM public.av_actividades_pub JOIN *.a_importadas USING (id_activ);
