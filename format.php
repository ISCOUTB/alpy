<?php
/**
 * Main display file for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $course;

// Validate required variables.
if (!isset($course) || !is_object($course)) {
    throw new moodle_exception('invalidcourseid');
}

require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/completionlib.php');

$format = core_courseformat\base::instance($course);
$course = $format->get_course();
$context = context_course::instance($course->id);

// Make sure section 0 is created.
course_create_sections_if_missing($format->get_course(), 0);

$renderer = $PAGE->get_renderer('format_alpy');

if (!is_null($displaysection)) {
   $format->set_section_number($displaysection);
}

// In Moodle 4.x, we must ensure M.course.format is initialized for legacy JS compatibility.
// We use a stronger initialization that includes creating the namespace if it doesn't exist.
$js = "
if (typeof M.course === 'undefined') { M.course = {}; }
if (typeof M.course.format === 'undefined') { M.course.format = {}; }
M.course.format.get_config = function() {
    return {
        'container_node': 'ul',
        'container_class': 'topics',
        'section_node': 'li',
        'section_class': 'section'
    };
};
";
$PAGE->requires->js_init_code($js);

$outputclass = $format->get_output_classname('content');
$output = new $outputclass($format);
echo $renderer->render($output);
