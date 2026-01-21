<?php
/**
 * Cache definitions for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$definitions = [
    'learning_profiles' => [
        'mode' => cache_store::MODE_SESSION,
        'simplekeys' => true,
        'simpledata' => false,
        'ttl' => 3600,
    ],
    'activity_tags' => [
        'mode' => cache_store::MODE_REQUEST,
        'simplekeys' => true,
        'simpledata' => false,
        'staticacceleration' => true,
    ],
];
