<?php
/**
 * Custom cmlist class for the alpy course format.
 * Reorders activities for students while preserving core layout.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_alpy\output\courseformat\content\section;

use core_courseformat\output\local\content\section\cmlist as core_cmlist;
use renderer_base;

class cmlist extends core_cmlist {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(renderer_base $output): \stdClass {
        global $USER, $DB;

        $data = parent::export_for_template($output);

        $format = $this->format;
        $course = $format->get_course();
        $context = \context_course::instance($course->id);

        // Teachers keep default ordering.
        if (has_capability('moodle/course:update', $context)) {
            return $data;
        }

        if (empty($data->cms)) {
            return $data;
        }

        $cms = $data->cms;
        $original_order = array_map(function ($item) {
            return $item->cmitem->id; }, $cms);

        // Get learning style (cached per session)
        $learning_style = [];
        $cache = \cache::make('format_alpy', 'learning_profiles');
        $cachekey = 'user_' . (int)$USER->id;
        $learning_style = $cache->get($cachekey);

        if ($learning_style === false) {
            $learning_style = [];
            try {
                if ($DB->get_manager()->table_exists('learning_style')) {
                    $sql = "SELECT ap_active, ap_reflexivo, ap_sensorial, ap_intuitivo, ap_visual, ap_verbal, ap_secuencial, ap_global 
                            FROM {learning_style} 
                            WHERE `user` = :userid AND is_completed = 1
                            ORDER BY id DESC";
                    $records = $DB->get_records_sql($sql, ['userid' => (int)$USER->id], 0, 1);

                    if ($records) {
                        $record = reset($records);
                        $learning_style = [
                            'active' => (int) $record->ap_active,
                            'reflexive' => (int) $record->ap_reflexivo,
                            'sensitive' => (int) $record->ap_sensorial,
                            'intuitive' => (int) $record->ap_intuitivo,
                            'visual' => (int) $record->ap_visual,
                            'verbal' => (int) $record->ap_verbal,
                            'sequential' => (int) $record->ap_secuencial,
                            'global' => (int) $record->ap_global,
                        ];
                    }
                }
            } catch (\Exception $e) {
                debugging('Error getting learning style: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
            $cache->set($cachekey, $learning_style);
        }

        if (empty($learning_style)) {
            return $data;
        }

        // Load tags for activities (cached per request)
        $cmids = array_map(function ($item) {
            return $item->cmitem->id; }, $cms);

        $tagcache = \cache::make('format_alpy', 'activity_tags');
        $tagcachekey = 'section_' . (int)$this->section->id;
        $alltags = $tagcache->get($tagcachekey);

        if ($alltags === false) {
            $alltags = \core_tag_tag::get_items_tags('core', 'course_modules', $cmids);
            $tagcache->set($tagcachekey, $alltags);
        }

        // Get resource weights from the single source of truth
        $resource_weights = \format_alpy::get_resource_weights();

        $scores = [];
        foreach ($cmids as $cmid) {
            $scores[$cmid] = 0;
            foreach ($alltags[$cmid] ?? [] as $tag) {
                // Resolve tag to canonical English key
                $tagkey = \format_alpy::resolve_resource_key($tag->rawname);
                if ($tagkey && isset($resource_weights[$tagkey])) {
                    foreach ($learning_style as $key => $value) {
                        $scores[$cmid] += $value * $resource_weights[$tagkey][$key];
                    }
                }
            }
        }

        usort($cms, function ($a, $b) use ($scores, $original_order) {
            $aid = $a->cmitem->id;
            $bid = $b->cmitem->id;
            $diff = ($scores[$bid] ?? 0) - ($scores[$aid] ?? 0);
            return $diff !== 0 ? $diff : array_search($aid, $original_order) - array_search($bid, $original_order);
        });

        $data->cms = $cms;
        return $data;
    }
}
