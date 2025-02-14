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
 * Filter sectionnames privacy tests.
 *
 * @package    filter_sectionnames
 * @copyright  eWallah.net (www.eWallah.net)
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_sectionnames\privacy;

use core_privacy\tests\provider_testcase;

/**
 * Filter sectionnames privacy tests.
 *
 * @package    filter_sectionnames
 * @copyright  eWallah.net (www.eWallah.net)
 * @author     Renaat Debleu <info@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class privacy_test extends provider_testcase {
    /**
     * Test returning metadata.
     * #[CoversClass(filter_sectionnames\privacy\provider)]
     */
    public function test_get_metadata(): void {
        $privacy = new provider();
        $reason = $privacy->get_reason();
        $this->assertEquals($reason, 'privacy:metadata');
        $this->assertStringContainsString('does not save', get_string($reason, 'filter_sectionnames'));
    }
}
