-- =====================================================
-- Tabla: repositorio_documentos
-- Descripción: Almacena los planes de acción y documentos
--              asociados a cada planta (sede)
-- =====================================================

CREATE TABLE IF NOT EXISTS `repositorio_documentos` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sede_id` INT(11) UNSIGNED NOT NULL,
  `titulo` VARCHAR(255) NOT NULL COMMENT 'Título del documento',
  `tipo` VARCHAR(50) NOT NULL DEFAULT 'documento' COMMENT 'Tipo: plan_accion, documento, reporte, otro',
  `descripcion` TEXT NULL COMMENT 'Descripción opcional del documento',
  `nombre_archivo` VARCHAR(255) NOT NULL COMMENT 'Nombre del archivo guardado en el servidor',
  `ruta_archivo` VARCHAR(500) NOT NULL COMMENT 'Ruta relativa del archivo (ej: uploads/repositorio/archivo.pdf)',
  `tamaño_archivo` INT(11) UNSIGNED NULL COMMENT 'Tamaño del archivo en bytes',
  `tipo_mime` VARCHAR(100) NULL COMMENT 'Tipo MIME del archivo (ej: application/pdf)',
  `fecha_documento` DATETIME NULL COMMENT 'Fecha del documento original (si aplica)',
  `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Fecha de creación del registro',
  `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Fecha de última actualización',
  PRIMARY KEY (`id`),
  INDEX `idx_sede_id` (`sede_id`),
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_repositorio_documentos_sede` 
    FOREIGN KEY (`sede_id`) 
    REFERENCES `sedes` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Repositorio de planes de acción y documentos por planta';

