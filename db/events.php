<?php
/**
 * Event handlers for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname'   => '\core\event\course_updated',
        'callback'    => '\format_alpy\observer::course_updated',
    ),
    array(
        'eventname'   => '\core\event\course_section_created',
        'callback'    => '\format_alpy\observer::course_section_created',
    ),
    array(
        'eventname'   => '\core\event\course_section_deleted',
        'callback'    => '\format_alpy\observer::course_section_deleted',
    ),
    array(
        'eventname'   => '\core\event\course_created',
        'callback'    => '\format_alpy\observer::course_created',
    ),
);
