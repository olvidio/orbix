-- Renombres en global/public y columna idioma en actividades publicadas (idempotente).
SELECT migracion_rename_columna('global', 'encargos', 'sf_sv', 'grupo_encargo');
SELECT migracion_rename_columna('public', 'u_centros', 'status', 'active');
SELECT migracion_rename_columna('public', 'u_centros', 'f_status', 'f_active');
SELECT migracion_rename_columna('public', 'x_locales', 'activo', 'active');
SELECT migracion_add_columna_si_no_existe('public', 'a_actividades_all', 'idioma', 'varchar(12) NULL');

CREATE OR REPLACE VIEW public.av_actividades_pub AS
  SELECT * FROM public.a_actividades_all a_actividades
  WHERE a_actividades.publicado = true;
