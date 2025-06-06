<?php
namespace format_alpy\output\courseformat;

use core_courseformat\output\local\content as content_base;
use renderer_base;
use stdClass;

class content extends content_base {

    public function export_for_template(renderer_base $output): stdClass {
        $format = $this->format;
        $course = $format->get_course();
        $modinfo = get_fast_modinfo($course);

        $sections = [];
        foreach ($modinfo->get_section_info_all() as $sectioninfo) {
            if (!$sectioninfo || !$sectioninfo->uservisible) {
                continue;
            }

            $sectionclass = $format->get_output_classname('content\\section');
            $section = new $sectionclass($format, $sectioninfo);

            $sections[] = $section->export_for_template($output);
        }

        $data = new stdClass();
        $data->sections = $sections;
        return $data;
    }

    public function get_template_name(renderer_base $output): string {
        return 'format_alpy/local/content';
    }
}

