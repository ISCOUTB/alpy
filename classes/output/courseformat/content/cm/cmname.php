<?php
/**
 * Renderable class for the module name in the alpy course format.
 *
 * @package    format_alpy
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_alpy\output\courseformat\content\cm;

use core_courseformat\output\local\content\cm\cmname as core_cmname;
use renderer_base;

class cmname extends core_cmname {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(renderer_base $output): array {
        $data = parent::export_for_template($output);
        
        global $CFG;
        $mod = $this->mod;

        // Normalize icon data for our custom template
        $iconurl = $data['icon'] ?? null;
        $iconstr = '';
        
        if ($iconurl instanceof \moodle_url) {
            $iconstr = $iconurl->out();
        } else if (is_string($iconurl)) {
            $iconstr = $iconurl;
        } else if (is_array($iconurl) && isset($iconurl['url'])) {
            $val = $iconurl['url'];
            if (is_string($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $iconstr = (string)$val;
            }
        } else if (is_object($iconurl) && method_exists($iconurl, '__toString')) {
            $iconstr = (string)$iconurl;
        }

        // Initialize icon structure with default values
        $normalizedIcon = [
            'iscustom' => false,
            'url' => $iconstr,
            'alt' => $data['pluginname'] ?? ($mod->modfullname ?? ''),
            'iconclass' => $data['iconclass'] ?? 'activityicon',
        ];
        
        // Check for custom icons in alpy format
        if ($mod && $this->format->get_course()->format === 'alpy') {
            $tags = \core_tag_tag::get_item_tags('core', 'course_modules', $mod->id);
            if ($tags) {
                // Ensure helper is loaded
                if (class_exists('format_alpy')) {
                    foreach ($tags as $tag) {
                        $tagname = \core_text::strtolower(trim($tag->rawname));
                        $canonical = \format_alpy::resolve_resource_key($tagname);
                        
                        if ($canonical) {
                            // Use canonical name for file lookup
                            $tagname = $canonical;
                            
                            // SECURITY: Validate tagname to prevent path traversal
                            if (!preg_match('/^[a-z0-9_]+$/', $tagname)) {
                                continue;
                            }
                            
                            // Check for SVG first
                            $svgpath = $CFG->dirroot . '/course/format/alpy/pix/' . $tagname . '.svg';
                            if (file_exists($svgpath)) {
                                $normalizedIcon['iscustom'] = true;
                                $normalizedIcon['url'] = $output->image_url($tagname, 'format_alpy')->out();
                                break;
                            }
                            
                            // Fallback to PNG
                            $pngpath = $CFG->dirroot . '/course/format/alpy/pix/' . $tagname . '.png';
                            if (file_exists($pngpath)) {
                                $normalizedIcon['iscustom'] = true;
                                $normalizedIcon['url'] = $output->image_url($tagname, 'format_alpy')->out();
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Keep core template compatibility: `icon` must be a string URL.
        $data['icon'] = $normalizedIcon['url'];

        // Ensure the icon class reflects custom icons for core template usage.
        if (!empty($normalizedIcon['iscustom'])) {
            $existingclass = $data['iconclass'] ?? '';
            $data['iconclass'] = trim($existingclass . ' alpy-custom-icon');
            
            // Unset purpose class to prevent Moodle default background colors from appearing
            $data['purpose'] = '';
        }

        // Provide structured icon data for the custom template.
        $data['icondata'] = $normalizedIcon;
        
        return $data;
    }

    /**
     * Get template name.
     *
     * @param renderer_base $output
     * @return string
     */
    public function get_template_name(renderer_base $output): string {
        return 'format_alpy/local/content/cm/cmname';
    }
}
