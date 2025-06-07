<?php
namespace format_alpy\output\courseformat\content\section;

use core_courseformat\output\local\content\section\cmitem as core_cmitem;
use renderer_base;
use stdClass;
use core_tag_tag;
use context_module;

class cmitem extends core_cmitem {

    public function export_for_template(renderer_base $output): stdClass {
        global $CFG;

        $data = parent::export_for_template($output);

        $context = context_module::instance($this->mod->id);
        $is = has_capability('moodle/course:update', $context);

        if ($this->format->get_course()->format !== 'alpy') {
            return $data;
        }


        if ($is) {
        // Obtener etiquetas del módulo
        $tags = core_tag_tag::get_item_tags('core', 'course_modules', $this->mod->id);
        $data->tags = [];
        foreach ($tags as $tag) {
            $data->tags[] = $tag->rawname;
        }
        }

        return $data;
    }

      public function get_template_name(\renderer_base $renderer): string {
        return 'format_alpy/local/content/section/cmitem';
    }

}

