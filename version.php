<?php
/**
 * Version details for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2026012100; // YYYYMMDDXX (year, month, day, 2-digit version number).
$plugin->requires = 2022041900; // Moodle 4.0+
$plugin->component = 'format_alpy';
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.0.1';

$plugin->dependencies = [
    'block_learning_style' => 2026010800,
];
