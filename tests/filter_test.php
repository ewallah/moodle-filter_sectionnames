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
 * @copyright  2021
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_sectionnames;

/**
 * Tests for filter_data.
 *
 * @package    filter_sectionnames
 * @copyright  2021
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \filter_sectionnames
 */
class filter_test extends \advanced_testcase {


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        \filter_manager::reset_caches();
        \filter_set_global_state('sectionnames', TEXTFILTER_ON);
        set_config('logo', 'mock.png', 'core_admin');
    }

    /**
     * Tests that the filter applies the required changes.
     *
     * @return void
     * @covers \filter_sectionnames
     */
    public function test_filter(): void {
        global $DB, $PAGE;
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $course = $dg->create_course(['numsections' => 5], ['createsections' => true]);
        $user = $dg->create_user();
        $dg->enrol_user($user->id, $course->id);
        $coursesections = $DB->get_records('course_sections', ['course' => $course->id]);
        $customname = "Custom Section";
        foreach ($coursesections as $section) {
            $section->name = "$customname $section->section";
            $DB->update_record('course_sections', $section);
        }
        $PAGE->set_course($course);
        $PAGE->set_url(course_get_url($course->id));
        $PAGE->set_context(\context_course::instance($course->id));

        $this->assertEquals(false, format_text(false, FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("$customname 1", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("$customname 2", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("$customname 3", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("$customname 4", FORMAT_HTML));
        $this->assertStringContainsString('class="autolink"', format_text("$customname 5", FORMAT_HTML));
        $this->assertStringNotContainsString('class="autolink"', format_text("$customname 6", FORMAT_HTML));
    }

    /**
     * Test strings.
     * @covers \filter_sectionnames
     */
    public function test_strings(): void {
        $this->assertNotEmpty(get_string('pluginname', 'filter_sectionnames'));
        $this->assertNotEmpty(get_string('filtername', 'filter_sectionnames'));
        $this->assertNotEmpty(get_string('privacy:metadata', 'filter_sectionnames'));
    }

    /**
     * Test plugin.
     * @covers \filter_sectionnames
     */
    public function test_plugin(): void {
        $class = new \ReflectionClass('filter_sectionnames');
        $this->assertCount(8, $class->getMethods());
        $this->assertCount(5, $class->getProperties());
    }
}
