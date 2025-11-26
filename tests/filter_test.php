<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests.
 *
 * @package    filter_sectionnames
 * @category   test
 * @copyright  eWallah.net (www.eWallah.net)
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_sectionnames;

use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Tests for filter_data.
 *
 * @package    filter_sectionnames
 * @copyright  eWallah.net (www.eWallah.net)
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(text_filter::class)]
final class filter_test extends \advanced_testcase {
    /**
     * Setup.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
        $this->setAdminUser();
        \filter_manager::reset_caches();
        \filter_set_global_state('sectionnames', TEXTFILTER_ON);
        set_config('logo', 'mock.png', 'core_admin');
    }

    /**
     * Tests that the filter applies the required changes.
     */
    public function test_filter(): void {
        global $DB, $PAGE;
        $dg = $this->getDataGenerator();
        $course = $dg->create_course(['numsections' => 5], ['createsections' => true]);
        $coursesections = $DB->get_records('course_sections', ['course' => $course->id]);
        $customname = "Custom Section &";
        foreach ($coursesections as $section) {
            $section->name = "{$customname} $section->section";
            $DB->update_record('course_sections', $section);
        }

        $PAGE->set_course($course);
        $PAGE->set_url(course_get_url($course->id));
        $PAGE->set_context(\context_course::instance($course->id));

        $this->assertEquals(false, format_text(false, FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("{$customname} 1", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("{$customname} 2", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("{$customname} 3", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("{$customname} 4", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("{$customname} 5", FORMAT_HTML));
        $this->assertStringNotContainsString('class="autolink"', format_text("{$customname} 6", FORMAT_HTML));
    }

    /**
     * Tests that the filter works in all contexts.
     */
    public function test_all_context(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $course = $dg->create_course(['numsections' => 3], ['createsections' => true]);
        $page = $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course]);
        $modinfo = get_fast_modinfo($course);
        $cms = $modinfo->get_instances();
        $cm = $cms['page'][$page->id];
        $coursesections = $DB->get_records('course_sections', ['course' => $course->id]);
        $customname = "Custom Section";
        foreach ($coursesections as $section) {
            $section->name = "{$customname} $section->section";
            $DB->update_record('course_sections', $section);
        }

        $filter = new \filter_sectionnames\text_filter(\context_course::instance($course->id), []);
        $this->assertEquals('false', $filter->filter('false', []));
        $this->assertEquals("{$customname} 1", $filter->filter("{$customname} 1", []));
        $filter = new \filter_sectionnames\text_filter(\context_user::instance($user->id), []);
        $this->assertEquals('false', $filter->filter('false', []));
        $this->assertEquals("{$customname} 1", $filter->filter("{$customname} 1", []));
        $filter = new \filter_sectionnames\text_filter(\context_module::instance($cm->id), []);
        $this->assertEquals('false', $filter->filter('false', []));
        $this->assertEquals("{$customname} 1", $filter->filter("{$customname} 1", []));
    }

    /**
     * Test strings.
     */
    public function test_strings(): void {
        $this->assertNotEmpty(get_string('pluginname', 'filter_sectionnames'));
        $this->assertNotEmpty(get_string('filtername', 'filter_sectionnames'));
        $this->assertNotEmpty(get_string('privacy:metadata', 'filter_sectionnames'));
    }

    /**
     * Test plugin.
     */
    public function test_plugin(): void {
        $class = new \ReflectionClass('filter_sectionnames\text_filter');
        $this->assertGreaterThan(8, $class->getMethods());
        $this->assertCount(5, $class->getProperties());
    }
}
