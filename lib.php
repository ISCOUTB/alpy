<?php
/**
 * Main library file for the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot. '/course/format/lib.php');
require_once($CFG->dirroot. '/course/lib.php');

class format_alpy extends core_courseformat\base {
    /**
     * Learning style weights for each resource type.
     * Keys match filenames in /pix/ (e.g. reading.png -> 'reading').
     * This is the single source of truth for resource scoring.
     *
     * @return array
     */
    public static function get_resource_weights(): array {
        return [
            'map'           => ['active' => 3, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'diagram'       => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'reading'       => ['active' => 3, 'reflexive' => 1, 'sensitive' => 2, 'intuitive' => 3, 'visual' => 2, 'verbal' => 3, 'sequential' => 2, 'global' => 3],
            'audio'         => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 1, 'verbal' => 3, 'sequential' => 1, 'global' => 2],
            'infographic'   => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 2],
            'videotutorial' => ['active' => 2, 'reflexive' => 2, 'sensitive' => 2, 'intuitive' => 1, 'visual' => 3, 'verbal' => 2, 'sequential' => 3, 'global' => 1],
            'conference'    => ['active' => 2, 'reflexive' => 2, 'sensitive' => 3, 'intuitive' => 3, 'visual' => 2, 'verbal' => 3, 'sequential' => 1, 'global' => 2],
            'animation'     => ['active' => 1, 'reflexive' => 1, 'sensitive' => 2, 'intuitive' => 1, 'visual' => 2, 'verbal' => 1, 'sequential' => 2, 'global' => 3],
            'simulation'    => ['active' => 2, 'reflexive' => 1, 'sensitive' => 3, 'intuitive' => 3, 'visual' => 3, 'verbal' => 1, 'sequential' => 2, 'global' => 2],
            'presentation'  => ['active' => 2, 'reflexive' => 1, 'sensitive' => 2, 'intuitive' => 2, 'visual' => 3, 'verbal' => 1, 'sequential' => 3, 'global' => 2],
            'journal'       => ['active' => 1, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'research'      => ['active' => 3, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 3, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'coding'        => ['active' => 2, 'reflexive' => 2, 'sensitive' => 3, 'intuitive' => 2, 'visual' => 2, 'verbal' => 2, 'sequential' => 2, 'global' => 2],
            'debate'        => ['active' => 3, 'reflexive' => 2, 'sensitive' => 2, 'intuitive' => 1, 'visual' => 1, 'verbal' => 3, 'sequential' => 2, 'global' => 1],
            'project'       => ['active' => 2, 'reflexive' => 2, 'sensitive' => 3, 'intuitive' => 1, 'visual' => 2, 'verbal' => 1, 'sequential' => 3, 'global' => 2],
            'written'       => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 3],
            'questionnaire' => ['active' => 3, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 3, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'quiz'          => ['active' => 3, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 3, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
        ];
    }

    /**
     * Spanish to English tag aliases for backwards compatibility.
     *
     * @return array
     */
    public static function get_tag_aliases(): array {
        return [
            // Spanish -> English
            'mapa'            => 'map',
            'diagrama'        => 'diagram',
            'lectura'         => 'reading',
            'infografia'      => 'infographic',
            'videoconferencia'=> 'conference',
            'animacion'       => 'animation',
            'simulacion'      => 'simulation',
            'presentacion'    => 'presentation',
            'diario'          => 'journal',
            'busqueda'        => 'research',
            'vpl'             => 'coding',
            'proyecto'        => 'project',
            'escrito'         => 'written',
            'cuestionario'    => 'questionnaire',
        ];
    }

    /**
     * Resolve a tag name to its canonical English key.
     * Accepts both English and Spanish tag names.
     *
     * @param string $tagname The raw tag name (e.g. 'lectura', 'reading', 'mapa')
     * @return string|null The canonical English key or null if not found
     */
    public static function resolve_resource_key($tagname): ?string {
        $tagname = \core_text::strtolower(trim($tagname));
        $weights = self::get_resource_weights();
        
        // Direct match (English key)
        if (array_key_exists($tagname, $weights)) {
            return $tagname;
        }
        
        // Check Spanish aliases
        $aliases = self::get_tag_aliases();
        if (isset($aliases[$tagname])) {
            return $aliases[$tagname];
        }

        return null;
    }

    /**
     * Backwards compatibility wrapper for get_resource_weights().
     * @deprecated Use get_resource_weights() instead
     * @return array
     */
    public static function get_resource_definitions(): array {
        $weights = self::get_resource_weights();
        $result = [];
        foreach ($weights as $key => $scores) {
            $result[$key] = ['scores' => $scores];
        }
        return $result;
    }

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    public function uses_course_index() {
        return true;
    }

    public function uses_indentation(): bool {
        return (get_config('format_alpy', 'indentation')) ? true : false;
    }

    /**
     * Returns the output class name for the specified type.
     *
     * @param string $type The output class type (e.g., 'content', 'content\\cm\\cmname')
     * @return string The fully qualified class name
     */
    public function get_output_classname(string $type): string {
        // Use our custom cmname class for activity names (includes custom icon logic)
        if ($type === 'content\\cm\\cmname') {
            return 'format_alpy\\output\\courseformat\\content\\cm\\cmname';
        }

        // Use custom cmlist class to reorder activities for students
        if ($type === 'content\\section\\cmlist') {
            return 'format_alpy\\output\\courseformat\\content\\section\\cmlist';
        }
        
        // For other types, use parent implementation
        return parent::get_output_classname($type);
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    public function page_title(): string {
        return get_string('sectionoutline');
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            // Return the name the user set.
            return format_string($section->name, true, array('context' => context_course::instance($this->courseid)));
        } else {
            return $this->get_default_section_name($section);
        }
    }

    /**
     * Returns the default section name for the alpy course format.
     *
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * Otherwise, the default format of "[start date] - [end date]" will be returned.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_alpy');
        } else {
            $dates = $this->get_section_dates($section);

            // Find the prior day for display purposes.
            $enddate = new DateTime();
            $enddate->setTimezone(core_date::get_user_timezone_object());
            $enddate->setTimestamp(intval($dates->end));
            $enddate->modify('-1 day');
            $dates->end = $enddate->getTimestamp();

            $dateformat = get_string('strftimedateshort');
            $weekday = userdate($dates->start, $dateformat);
            $endweekday = userdate($dates->end, $dateformat);
            return $weekday.' - '.$endweekday;
        }
    }

    /**
     * Returns the name for the highlighted section.
     *
     * @return string The name for the highlighted section based on the given course format.
     */
    public function get_section_highlighted_name(): string {
        return get_string('currentsection', 'format_alpy');
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section not empty, the function returns section page; otherwise, it returns course page.
     *     'sr' (int) used by course formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        $course = $this->get_course();
        if (array_key_exists('sr', $options) && !is_null($options['sr'])) {
            $sectionno = $options['sr'];
        } else if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        
        // Always return to the main course view page with section anchor
        // instead of redirecting to section.php
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);
        if ($sectionno !== null && $sectionno > 0) {
            $url->set_anchor('section-' . $sectionno);
        }
        
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    public function supports_components() {
        return true;
    }

    /**
     * Loads all of the course sections into the navigation
     *
     * @param global_navigation $navigation
     * @param navigation_node $node The course node within the navigation
     */
    public function extend_course_navigation($navigation, navigation_node $node) {
        global $PAGE;
        // if section is specified in course/view.php, make sure it is expanded in navigation
        if ($navigation->includesectionnum === false) {
            $selectedsection = optional_param('section', null, PARAM_INT);
            if ($selectedsection !== null && (!defined('AJAX_SCRIPT') || AJAX_SCRIPT == '0') &&
                    $PAGE->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
                $navigation->includesectionnum = $selectedsection;
            }
        }
        parent::extend_course_navigation($navigation, $node);

        // We want to remove the general section if it is empty.
        $modinfo = get_fast_modinfo($this->get_course());
        $sections = $modinfo->get_sections();
        if (!isset($sections[0])) {
            // The general section is empty to find the navigation node for it we need to get its ID.
            $section = $modinfo->get_section_info(0);
            $generalsection = $node->get($section->id, navigation_node::TYPE_SECTION);
            if ($generalsection) {
                // We found the node - now remove it.
                $generalsection->remove();
            }
        }
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $current = -1;
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
                if ($this->is_section_current($section)) {
                    $current = $number;
                }
            }
        }
        return array('sectiontitles' => $titles, 'current' => $current, 'action' => 'move');
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array()
        );
    }

    /**
     * Definitions of the additional options that this course format uses for course.
     *
     * Alpy format uses the following options:
     * - coursedisplay
     * - hiddensections
     * - automaticenddate
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'setup_mode' => array(
                    'default' => 1,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay ?? COURSE_DISPLAY_SINGLEPAGE,
                    'type' => PARAM_INT,
                ),
                'automaticenddate' => array(
                    'default' => 0,
                    'type' => PARAM_BOOL,
                ),
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseformatoptionsedit = array(
                'setup_mode' => array(
                    'label' => new lang_string('setup_mode', 'format_alpy'),
                    'help' => 'setup_mode',
                    'help_component' => 'format_alpy',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('mode_academic', 'format_alpy'),
                            0 => new lang_string('mode_manual', 'format_alpy')
                        )
                    ),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            COURSE_DISPLAY_SINGLEPAGE => new lang_string('coursedisplay_single'),
                            COURSE_DISPLAY_MULTIPAGE => new lang_string('coursedisplay_multi')
                        )
                    ),
                    'help' => 'coursedisplay',
                    'help_component' => 'moodle',
                ),
                'automaticenddate' => array(
                    'label' => new lang_string('automaticenddate', 'format_alpy'),
                    'help' => 'automaticenddate',
                    'help_component' => 'format_alpy',
                    'element_type' => 'advcheckbox',
                )
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@link course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $COURSE, $PAGE;
        $elements = parent::create_edit_form_elements($mform, $forsection);

        if (!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID) && !$mform->elementExists('numsections')) {
            // Add "numsections" element only when creating a new course.
            // For existing courses, sections should be managed from the course page.
            $courseconfig = get_config('moodlecourse');
            $max = (int)$courseconfig->maxsections;
            $element = $mform->addElement('select', 'numsections', get_string('numberweeks'), range(0, $max ?: 52));
            $mform->setType('numsections', PARAM_INT);
            if (is_null($mform->getElementValue('numsections'))) {
                $mform->setDefault('numsections', $courseconfig->numsections);
            }
            array_unshift($elements, $element);
        }

            // No manual reordering required: setup_mode is first in format options list.

        // Force order: setup_mode first, numsections right after (when present).
        if ($mform->elementExists('setup_mode') && $mform->elementExists('numsections') && $mform->elementExists('hiddensections')) {
            $setup_mode_el = $mform->removeElement('setup_mode', false);
            $numsections_el = $mform->removeElement('numsections', false);

            if ($numsections_el) {
                $mform->insertElementBefore($numsections_el, 'hiddensections');
            }
            if ($setup_mode_el) {
                $mform->insertElementBefore($setup_mode_el, 'numsections');
            }
        }

        // Hide numsections when academic mode is selected (only on new course form).
        if ($mform->elementExists('numsections')) {
            $mform->hideIf('numsections', 'setup_mode', 'eq', 1);
        }

        // Remove setup_mode from edit form (only show during course creation).
        if (!empty($COURSE->id) && $COURSE->id != SITEID) {
            $mform->removeElement('setup_mode');
        }

        // Disabled Rules
        $mform->disabledIf('startdate', 'setup_mode', 'eq', 1);
        $mform->disabledIf('enddate', 'automaticenddate', 'checked');

        // Return only valid elements to avoid null dereferences.
        $elements = array_values(array_filter($elements, function($el) {
            return !empty($el) && is_object($el) && $el instanceof HTML_QuickForm_element;
        }));

        // Enforce order within format options: setup_mode first, then numsections (if present).
        $byname = [];
        foreach ($elements as $el) {
            $name = $el->getName();
            if ($mform->elementExists($name)) {
                $byname[$name] = $el;
            }
        }

        $ordered = [];
        foreach (['setup_mode', 'numsections'] as $priorityname) {
            if (isset($byname[$priorityname])) {
                $ordered[] = $byname[$priorityname];
            }
        }
        foreach ($elements as $el) {
            $name = $el->getName();
            if (!isset($byname[$name])) {
                continue;
            }
            if (in_array($name, ['setup_mode', 'numsections'], true)) {
                continue;
            }
            $ordered[] = $el;
        }

        return $ordered;
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'weeks', we try to copy options
     * 'coursedisplay', 'numsections' and 'hiddensections' from the previous format.
     * If previous course format did not have 'numsections' option, we populate it with the
     * current number of sections
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update
     * @return bool whether there were any changes to the options values
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB;
        $data = (array)$data;

        // AUTO-CALCULATION LOGIC FOR ACADEMIC PERIODS
        if (isset($data['setup_mode']) && $data['setup_mode'] == 1) { // 1 = Academic Mode (Auto)
            $current_year = date('Y');
            $current_month = date('n');

            // Logic: If June (6) or later, assume Second Semester (Aug). Else First Semester (Feb).
            if ($current_month >= 6) {
                // Second semester: Starts first Monday of August
                $start_month = 8; // August
            } else {
                // First semester: Starts first Monday of February
                $start_month = 2; // February
            }

            // Calculate First Monday
            $start_date_str = "first monday of " . date("F", mktime(0, 0, 0, $start_month, 10)) . " $current_year";
            $start_timestamp = strtotime($start_date_str);

            // Override data
            $data['startdate'] = $start_timestamp;
            $data['numsections'] = 16; // Fixed 16 weeks
            // Do not force automatic end date; allow manual end date if desired.
            
            // NOTE: The actual creation of sections and DB updates for new courses 
            // is handled by the course_created observer to avoid race conditions.
            // For existing courses updates, we do it here if possible.
            if (!empty($data['id'])) {
                 // Only run this if we are SURE the record exists (update mode)
                 // Check if record exists to avoid crash on new course creation flows
                 if ($DB->record_exists('course', ['id' => $data['id']])) {
                    $DB->set_field('course', 'startdate', $start_timestamp, ['id' => $data['id']]);
                    rebuild_course_cache($data['id'], true);
                    $course = $DB->get_record('course', ['id' => $data['id']]);
                    course_create_sections_if_missing($course, range(1, 16));
                    \format_alpy::update_end_date($data['id']); 
                 }
            }
        } else {
            // MANUAL MODE: Enforce start date to calculate to the NEXT Monday if not already a Monday
            if (isset($data['startdate'])) {
                 $startdate = $data['startdate'];
                 // Check if the selected date is a Monday (1 = Monday in 'N' format)
                 if (date('N', $startdate) != 1) {
                     // It's not a Monday, move to next Monday
                     $new_start = strtotime('next monday', $startdate);
                     $data['startdate'] = $new_start;
                     
                     if (!empty($data['id']) && $DB->record_exists('course', ['id' => $data['id']])) {
                         $DB->set_field('course', 'startdate', $new_start, ['id' => $data['id']]);
                         rebuild_course_cache($data['id'], true);
                         \format_alpy::update_end_date($data['id']);
                     }
                 }
            }
        }

        if ($oldcourse !== null) {
            $oldcourse = (array)$oldcourse;
            $options = $this->course_format_options();
            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    }
                }
            }
            // If numsections provided, ensure all sections exist (do not delete any).
            if (!empty($data['numsections']) && !empty($data['id']) && $DB->record_exists('course', ['id' => $data['id']])) {
                $numsections = (int)$data['numsections'];
                if ($numsections > 0) {
                    $course = $DB->get_record('course', ['id' => $data['id']]);
                    course_create_sections_if_missing($course, range(1, $numsections));
                }
            }
        }
        return $this->update_format_options($data);
    }

    /**
     * Return the start and end date of the passed section
     *
     * @param int|stdClass|section_info $section section to get the dates for
     * @param int $startdate Force course start date, useful when the course is not yet created
     * @return stdClass property start for startdate, property end for enddate
     */
    public function get_section_dates($section, $startdate = false) {
        global $USER;

        if ($startdate === false) {
            $course = $this->get_course();
            $userdates = course_get_course_dates_for_user_id($course, $USER->id);
            $startdate = $userdates['start'];
        }

        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }

        // Create a DateTime object for the start date.
        $startdateobj = new DateTime("@$startdate");
        $startdateobj->setTimezone(core_date::get_user_timezone_object());

        // Calculate the interval for one week.
        $oneweekinterval = new DateInterval('P7D');

        // Calculate the interval for the specified number of sections.
        for ($i = 1; $i < $sectionnum; $i++) {
            $startdateobj->add($oneweekinterval);
        }

        // Calculate the end date.
        $enddateobj = clone $startdateobj;
        $enddateobj->add($oneweekinterval);

        $dates = new stdClass();
        $dates->start = $startdateobj->getTimestamp();
        $dates->end = $enddateobj->getTimestamp();

        return $dates;
    }

    /**
     * Returns true if the specified section is current.
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function is_section_current($section) {
        if (is_object($section)) {
            $sectionnum = $section->section;
        } else {
            $sectionnum = $section;
        }
        if ($sectionnum < 1) {
            return false;
        }
        $timenow = time();
        $dates = $this->get_section_dates($section);
        return (($timenow >= $dates->start) && ($timenow < $dates->end));
    }

    /**
     * Whether this format allows to delete sections
     *
     * Do not call this function directly, instead use {@link course_can_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * Returns the default end date for alpy course format.
     *
     * @param moodleform $mform
     * @param array $fieldnames The form - field names mapping.
     * @return int
     */
    public function get_default_course_enddate($mform, $fieldnames = array()) {

        if (empty($fieldnames['startdate'])) {
            $fieldnames['startdate'] = 'startdate';
        }

        if (empty($fieldnames['numsections'])) {
            $fieldnames['numsections'] = 'numsections';
        }

        $startdate = $this->get_form_start_date($mform, $fieldnames);
        if ($mform->elementExists($fieldnames['numsections'])) {
            $numsections = $mform->getElementValue($fieldnames['numsections']);
            $numsections = $mform->getElement($fieldnames['numsections'])->exportValue($numsections);
        } else if ($this->get_courseid()) {
            // For existing courses get the number of sections.
            $numsections = $this->get_last_section_number();
        } else {
            // Fallback to the default value for new courses.
            $numsections = get_config('moodlecourse', $fieldnames['numsections']);
        }

        // Final week's last day.
        $dates = $this->get_section_dates(intval($numsections), $startdate);
        return $dates->end;
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     *
     * @param stdClass|cm_info $cm course module (may be null if we are displaying a form for adding a module)
     * @param stdClass|section_info $section section where this module is located or will be added to
     * @return bool
     */
    public function allow_stealth_module_visibility($cm, $section) {
        // Allow the third visibility state inside visible sections or in section 0.
        return !$section->section || $section->visible;
    }

    public function section_action($section, $action, $sr) {
        global $PAGE;

        // Call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_alpy');

        if (!($section instanceof section_info)) {
            $modinfo = course_modinfo::instance($this->courseid);
            $section = $modinfo->get_section_info($section->section);
        }
        $elementclass = $this->get_output_classname('content\\section\\availability');
        $availability = new $elementclass($this, $section);

        $rv['section_availability'] = $renderer->render($availability);

        return $rv;
    }

    /**
     * Updates the end date for a course in weeks format if option automaticenddate is set.
     *
     * This method is called from event observers and it can not use any modinfo or format caches because
     * events are triggered before the caches are reset.
     *
     * @param int $courseid
     */
    public static function update_end_date($courseid) {
        global $DB, $COURSE;

        // Use one DB query to retrieve necessary fields in course, value for automaticenddate and number of the last
        // section. This query will also validate that the course is indeed in 'weeks' format.
        $insql = "SELECT c.id, c.format, c.startdate, c.enddate, MAX(s.section) AS lastsection
                    FROM {course} c
                    JOIN {course_sections} s
                      ON s.course = c.id
                   WHERE c.format = :format
                     AND c.id = :courseid
                GROUP BY c.id, c.format, c.startdate, c.enddate";
        $sql = "SELECT co.id, co.format, co.startdate, co.enddate, co.lastsection, fo.value AS automaticenddate
                  FROM ($insql) co
             LEFT JOIN {course_format_options} fo
                    ON fo.courseid = co.id
                   AND fo.format = co.format
                   AND fo.name = :optionname
                   AND fo.sectionid = 0";
        $course = $DB->get_record_sql($sql,
            ['optionname' => 'automaticenddate', 'format' => 'alpy', 'courseid' => $courseid]);

        if (!$course) {
            // Looks like it is a course in a different format, nothing to do here.
            return;
        }

        // Create an instance of this class and mock the course object.
        $format = new format_alpy('alpy', $courseid);
        $format->course = $course;

        // If automaticenddate is not specified take the default value.
        if (!isset($course->automaticenddate)) {
            $defaults = $format->course_format_options();
            $course->automaticenddate = $defaults['automaticenddate']['default'];
        }

        // Check that the course format for setting an automatic date is set.
        if (!empty($course->automaticenddate)) {
            // Get the final week's last day.
            $dates = $format->get_section_dates((int)$course->lastsection);

            // Set the course end date.
            if ($course->enddate != $dates->end) {
                $DB->set_field('course', 'enddate', $dates->end, array('id' => $course->id));
                if (isset($COURSE->id) && $COURSE->id == $courseid) {
                    $COURSE->enddate = $dates->end;
                }
            }
        }
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of configuration settings
     * @since Moodle 3.5
     */
    public function get_config_for_external() {
        // Return everything (nothing to hide).
        $formatoptions = $this->get_format_options();
        $formatoptions['indentation'] = get_config('format_alpy', 'indentation');
        return $formatoptions;
    }

    /**
     * Get the required javascript files for the course format.
     *
     * @return array The list of javascript files required by the course format.
     */
    public function get_required_jsfiles(): array {
        return [];
    }
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_alpy_inplace_editable($itemtype, $itemid, $newvalue) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    
    // SECURITY: Validate and sanitize inputs
    $itemid = (int)$itemid;
    if ($itemid <= 0) {
        throw new \moodle_exception('invaliditemid', 'error');
    }
    
    // SECURITY: Only allow known item types
    if ($itemtype !== 'sectionname' && $itemtype !== 'sectionnamenl') {
        throw new \moodle_exception('invaliditemtype', 'error');
    }
    
    // SECURITY: Sanitize newvalue - clean_param for text
    $newvalue = clean_param($newvalue, PARAM_TEXT);
    
    $section = $DB->get_record_sql(
        'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
        [$itemid, 'alpy'], MUST_EXIST);
    
    // SECURITY: Verify user has capability to edit
    $context = \context_course::instance($section->course);
    require_capability('moodle/course:update', $context);
    
    return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
}
