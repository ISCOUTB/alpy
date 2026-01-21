<?php
/**
 * Privacy provider for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_alpy\privacy;

use core_privacy\local\metadata\null_provider;

class provider implements null_provider {
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return "privacy:metadata";
    }
}
