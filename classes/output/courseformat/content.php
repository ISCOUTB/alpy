<?php
/**
 * Renderable class for the course content in the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_alpy\output\courseformat;

use core_courseformat\output\local\content as content_base;
use renderer_base;
use stdClass;

class content extends content_base
{

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass
    {
        $data = parent::export_for_template($output);

        // Use core weekly CSS classes to preserve default Moodle layout.
        $data->format = 'weeks';

        return $data;
    }

    /**
     * Get the template name.
     *
     * @param renderer_base $output
     * @return string
     */
    public function get_template_name(renderer_base $output): string
    {
        return 'core_courseformat/local/content';
    }
}
