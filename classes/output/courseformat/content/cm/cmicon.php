<?php
namespace format_alpy\output\courseformat\content\cm;

use core_courseformat\output\local\content\cm\cmicon as core_cmicon;
use renderer_base;

class cmicon extends core_cmicon {

    public function export_for_template(renderer_base $output): array {
        global $CFG;

        $mod = $this->mod;

        $defaulticon = parent::export_for_template($output);

        if (!$mod || $this->format->get_course()->format !== 'alpy') {
            return $defaulticon;
        }

        $tags = \core_tag_tag::get_item_tags('core', 'course_modules', $mod->id);
        if ($tags) {
            foreach ($tags as $tag) {
                $tagname = strtolower($tag->rawname);
                $customicons = ['lectura', 'presentacion', 'videotutorial', 'simulacion', 'proyecto'];

                if (in_array($tagname, $customicons)) {
                    return [
                        'icon' => $CFG->wwwroot . "/blocks/learning_style/pix/{$tagname}.png",
                        'iconclass' => '',
                        'purpose' => 'content',
                    ];
                }
            }
        }

        return $defaulticon;
    }
}


