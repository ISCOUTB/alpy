<?php
/**
 * Event observers for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_alpy;

use core\event\course_updated;
use core\event\course_section_created;
use core\event\course_section_deleted;

class observer {

    /**
     * Triggered via \core\event\course_updated event.
     *
     * @param course_updated $event
     */
    public static function course_updated(course_updated $event) {
        global $CFG;
        $courseid = (int)$event->courseid;
        if ($courseid > 0) {
            require_once($CFG->dirroot . '/course/format/alpy/lib.php');
            \format_alpy::update_end_date($courseid);
        }
    }

    /**
     * Triggered via \core\event\course_section_created event.
     *
     * @param course_section_created $event
     */
    public static function course_section_created(course_section_created $event) {
        global $CFG;
        $courseid = (int)$event->courseid;
        if ($courseid > 0) {
            require_once($CFG->dirroot . '/course/format/alpy/lib.php');
            \format_alpy::update_end_date($courseid);
        }
    }

    /**
     * Triggered via \core\event\course_section_deleted event.
     *
     * @param course_section_deleted $event
     */
    public static function course_section_deleted(course_section_deleted $event) {
        global $CFG;
        $courseid = (int)$event->courseid;
        if ($courseid > 0) {
            require_once($CFG->dirroot . '/course/format/alpy/lib.php');
            \format_alpy::update_end_date($courseid);
        }
    }

    /**
     * Triggered via \core\event\course_created event.
     * This handles the initialization of Academic Period logic for NEW courses.
     *
     * @param \core\event\course_created $event
     */
    public static function course_created(\core\event\course_created $event) {
        global $DB;
        $courseid = (int)$event->objectid;
        
        // SECURITY: Validate courseid
        if ($courseid <= 0) {
            return;
        }
        
        $course = $DB->get_record('course', ['id' => $courseid]);
        if (!$course) {
            return;
        }

        // Only proceed if the course format is 'alpy'
        if ($course->format !== 'alpy') {
            return;
        }
        
        // We need to check the format options that were just saved
        // Since $course object might not have complete format options, we fetch them
        $format = course_get_format($course);
        $options = $format->get_format_options();

        if (isset($options['setup_mode']) && $options['setup_mode'] == 1) {
            // Apply Academic Period Logic
            $current_year = date('Y');
            $current_month = date('n');

            if ($current_month >= 6) {
                $start_month = 8; // August
            } else {
                $start_month = 2; // February
            }

            $start_date_str = "first monday of " . date("F", mktime(0, 0, 0, $start_month, 10)) . " $current_year";
            $start_timestamp = strtotime($start_date_str);

            // 1. Update Start Date
            $DB->set_field('course', 'startdate', $start_timestamp, ['id' => $courseid]);
            
            // 2. Refresh Cache
            rebuild_course_cache($courseid, true);
            $course = $DB->get_record('course', ['id' => $courseid]);

            // 3. Create 16 sections
            // Direct DB update for format options to avoid 'protected method' errors
            $opt_numsections = $DB->get_record('course_format_options', ['courseid' => $courseid, 'format' => 'alpy', 'name' => 'numsections']);
            if ($opt_numsections) {
                $opt_numsections->value = 16;
                $DB->update_record('course_format_options', $opt_numsections);
            } else {
                $DB->insert_record('course_format_options', (object)['courseid' => $courseid, 'format' => 'alpy', 'name' => 'numsections', 'value' => 16, 'sectionid' => 0]);
            }

            $opt_setupmode = $DB->get_record('course_format_options', ['courseid' => $courseid, 'format' => 'alpy', 'name' => 'setup_mode']);
            if ($opt_setupmode) {
                $opt_setupmode->value = 1;
                $DB->update_record('course_format_options', $opt_setupmode);
            } else {
                $DB->insert_record('course_format_options', (object)['courseid' => $courseid, 'format' => 'alpy', 'name' => 'setup_mode', 'value' => 1, 'sectionid' => 0]);
            }
            
            course_create_sections_if_missing($course, range(1, 16));

            // 4. Update end date only if automatic end date is enabled
            rebuild_course_cache($courseid, true); // Rebuild again to see new sections
            if (!empty($options['automaticenddate'])) {
                \format_alpy::update_end_date($courseid);
            }
        } else {
             // Manual Mode Logic: Ensure Monday start
             if (date('N', $course->startdate) != 1) {
                $new_start = strtotime('next monday', $course->startdate);
                $DB->set_field('course', 'startdate', $new_start, ['id' => $courseid]);
                rebuild_course_cache($courseid, true);
                if (!empty($options['automaticenddate'])) {
                    \format_alpy::update_end_date($courseid);
                }
             }
        }
    }
}
