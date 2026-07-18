-- Renombres active/f_active en centros (equivalente sf de 202604100000 …__sv.sql).
-- En sf la tabla padre es publicf.u_centros; el RENAME se propaga a hijas por herencia.
SELECT migracion_rename_columna('publicf', 'u_centros', 'status', 'active');
SELECT migracion_rename_columna('publicf', 'u_centros', 'f_status', 'f_active');
