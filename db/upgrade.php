<?php
/**
 * Upgrade scripts for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_format_alpy_upgrade($oldversion) {
    global $CFG, $DB;

    // SECURITY: Validate oldversion parameter
    $oldversion = (int)$oldversion;
    if ($oldversion < 0) {
        throw new moodle_exception('invalidparameter', 'error');
    }

    $dbman = $DB->get_manager();

    // Put any upgrade step following this.

    return true;
}
