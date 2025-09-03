<?php
namespace format_alpy\output\courseformat\content;

use core_courseformat\output\local\content\section as core_section;
use renderer_base;
use stdClass;
use core_courseformat\output\local\content\cm;
use context_course;
use core_course\output\section_renderer;
use moodle_url;

class section extends core_section {




  public function export_for_template(renderer_base $output): stdClass {
        global $USER, $DB, $PAGE;

        $format = $this->format;
        $course = $format->get_course();
        $section = $this->section;
        $summary = new $this->summaryclass($format, $section);



        $modinfo = get_fast_modinfo($course);
        $sectioninfo = $modinfo->get_section_info($section->section);
        $allcms = $modinfo->get_cms();

        $cms = array_filter($allcms, function($mod) use ($section) {
             return $mod->uservisible && $mod->sectionnum == $section->section;
        });

        

        $recurso = [
            'mapa' => ['active' => 3, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'diagrama' => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'lectura' => ['active' => 3, 'reflexive' => 1, 'sensitive' => 2, 'intuitive' => 3, 'visual' => 2, 'verbal' => 3, 'sequential' => 2, 'global' => 3],
            'audio' => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 1, 'verbal' => 3, 'sequential' => 1, 'global' => 2],
            'infografia' => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 2],
            'videotutorial' => ['active' => 2, 'reflexive' => 2, 'sensitive' => 2, 'intuitive' => 1, 'visual' => 3, 'verbal' => 2, 'sequential' => 3, 'global' => 1],
            'videoconferencia' => ['active' => 2, 'reflexive' => 2, 'sensitive' => 3, 'intuitive' => 3, 'visual' => 2, 'verbal' => 3, 'sequential' => 1, 'global' => 2],
            'animacion' => ['active' => 1, 'reflexive' => 1, 'sensitive' => 2, 'intuitive' => 1, 'visual' => 2, 'verbal' => 1, 'sequential' => 2, 'global' => 3],
            'simulacion' => ['active' => 2, 'reflexive' => 1, 'sensitive' => 3, 'intuitive' => 3, 'visual' => 3, 'verbal' => 1, 'sequential' => 2, 'global' => 2],
            'presentacion' => ['active' => 2, 'reflexive' => 1, 'sensitive' => 2, 'intuitive' => 2, 'visual' => 3, 'verbal' => 1, 'sequential' => 3, 'global' => 2],
            'diario' => ['active' => 1, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'busqueda' => ['active' => 3, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 3, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'vpl' => ['active' => 2, 'reflexive' => 2, 'sensitive' => 3, 'intuitive' => 2, 'visual' => 2, 'verbal' => 2, 'sequential' => 2, 'global' => 2],
            'debate' => ['active' => 3, 'reflexive' => 2, 'sensitive' => 2, 'intuitive' => 1, 'visual' => 1, 'verbal' => 3, 'sequential' => 2, 'global' => 1],
            'proyecto' => ['active' => 2, 'reflexive' => 2, 'sensitive' => 3, 'intuitive' => 1, 'visual' => 2, 'verbal' => 1, 'sequential' => 3, 'global' => 2],
            'escrito' => ['active' => 1, 'reflexive' => 1, 'sensitive' => 1, 'intuitive' => 1, 'visual' => 3, 'verbal' => 1, 'sequential' => 1, 'global' => 3],
            'cuestionario' => ['active' => 3, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 3, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
            'quiz' => ['active' => 3, 'reflexive' => 3, 'sensitive' => 1, 'intuitive' => 3, 'visual' => 1, 'verbal' => 1, 'sequential' => 1, 'global' => 1],
        ];

        $sql = "SELECT * FROM {learning_style} WHERE user = ? AND course = ? ORDER BY id DESC";
        $records = $DB->get_records_sql($sql, [$USER->id, $course->id], 0, 1);

        $learning_style = [];
        if ($records) {
            $record = reset($records);
            $learning_style = [
                'active' => $record->act_ref[1] === 'a' ? (int) preg_replace('/[^0-9]/', '', $record->act_ref) : 0,
                'reflexive' => $record->act_ref[1] !== 'a' ? (int) preg_replace('/[^0-9]/', '', $record->act_ref) : 0,
                'sensitive' => $record->sen_int[1] === 'a' ? (int) preg_replace('/[^0-9]/', '', $record->sen_int) : 0,
                'intuitive' => $record->sen_int[1] !== 'a' ? (int) preg_replace('/[^0-9]/', '', $record->sen_int) : 0,
                'visual' => $record->vis_vrb[1] === 'a' ? (int) preg_replace('/[^0-9]/', '', $record->vis_vrb) : 0,
                'verbal' => $record->vis_vrb[1] !== 'a' ? (int) preg_replace('/[^0-9]/', '', $record->vis_vrb) : 0,
                'sequential' => $record->seq_glo[1] === 'a' ? (int) preg_replace('/[^0-9]/', '', $record->seq_glo) : 0,
                'global' => $record->seq_glo[1] !== 'a' ? (int) preg_replace('/[^0-9]/', '', $record->seq_glo) : 0,
            ];
        }

        $scores = [];
        foreach ($cms as $mod) {
            $tags = \core_tag_tag::get_item_tags('core', 'course_modules', $mod->id);
            $scores[$mod->id] = 0;

            foreach ($tags as $tag) {
                $tagname = strtolower($tag->rawname);
                if (array_key_exists($tagname, $recurso)) {
                    foreach ($learning_style as $key => $value) {
                        $scores[$mod->id] += $value * $recurso[$tagname][$key];
                    }
                }
            }
        }



        usort($cms, function($a, $b) use ($scores) {
              return ($scores[$b->id] ?? 0) <=> ($scores[$a->id] ?? 0);
        });



        $modules = [];

        foreach ($cms as $mod) {

        if (!$mod->uservisible) {
            continue;
        }
    $renderedcm = $output->course_section_updated_cm_item($format, $section, $mod);
    $modules[] = (object)[ 'content' => $renderedcm ];
    }

    $data = (object)[
        'num' => $section->section ?? '0',
        'id' => $section->id,
        'sectionreturnnum' => $format->get_section_number(),
        'insertafter' => false,
        'summary' => $summary->export_for_template($output),
        'highlightedlabel' => $format->get_section_highlighted_name(),
        'sitehome' => $course->id == SITEID,
        'editing' => $PAGE->user_is_editing(),
        'displayonesection' => ($course->id != SITEID && $format->get_section_number() == $section->section),
        'sectionname' => $format->get_section_name($section),
    ];

    $haspartials = [];
    $haspartials['availability'] = $this->add_availability_data($data, $output);
    $haspartials['visibility'] = $this->add_visibility_data($data, $output);
    $haspartials['editor'] = $this->add_editor_data($data, $output);
    $haspartials['header'] = $this->add_header_data($data, $output); 
    $haspartials['content'] = true;
    $this->add_format_data($data, $haspartials, $output);
    $data->cms = $modules;
    $data->title = get_section_name($course, $section);
    $header = new \stdClass();
    $header->id = $section->id;
    $header->title = get_section_name($course, $section);
    $header->name = get_section_name($course, $section); // Texto sin HTML
    $header->url = new \moodle_url('/course/view.php', [
        'id' => $course->id,
        'section' => $section->section
    ]);
    $header->ishidden = !$sectioninfo->visible;
    $header->headinglevel = 3; // O el nivel que estés usando
    $header->displayonesection = false;
    $header->sitehome = false;
    $header->contentcollapsed = false;
    $header->editing = $PAGE->user_is_editing(); // O según la lógica del formato
 

    $addsection = new \core_courseformat\output\local\content\addsection($format);
    $data->numsections = $addsection->export_for_template($output);


    $data->header = $header;



        
        return $data;

  }


  public function get_template_name(\renderer_base $output): string {
   
    return 'format_alpy/local/content/section';
  }






}

