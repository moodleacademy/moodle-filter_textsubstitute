<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace filter_textsubstitute;

use filter_textsubstitute;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/textsubstitute/filter.php'); // Include the code to test.

/**
 * Unit tests for Text substitute filter.
 *
 * @package     filter_textsubstitute
 * @category    test
 * @copyright   2023 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_test extends \advanced_testcase {

    /**
     * Check that search terms are substituted with another given term when filtered.
     *
     * @param string $text Original text
     * @param string $filteredtextpattern Text after applying filter
     * @dataProvider filter_textsubstitute_provider
     *
     * @covers ::filter()
     */
    public function test_filter_textsubstitute($searchterm, $substituteterm, $formats, $originalformat, $inputtext, $expectedtext) {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Set the plugin config.
        set_config('searchterm', $searchterm, 'filter_textsubstitute');
        set_config('substituteterm', $substituteterm, 'filter_textsubstitute');
        set_config('formats', $formats, 'filter_textsubstitute');

        $filterplugin = new filter_textsubstitute(null, []);

        // Filter the text.
        $filteredtext = $filterplugin->filter($inputtext, ['originalformat' => $originalformat]);

        // Compare expected vs actual.
        $this->assertEquals($expectedtext, $filteredtext);
    }

    /**
     * Data provider for {@see test_filter_textsubstitute}
     *
     * @return string[]
     */
    public function filter_textsubstitute_provider(): array {
        return [
            'All formats allowed - html' => [
                'searchterm' => 'Moodle',
                'substituteterm' => 'Workplace',
                'formats' => FORMAT_HTML . ',' . FORMAT_MARKDOWN . ',' . FORMAT_MOODLE. ',' . FORMAT_PLAIN,
                'originalformat' => FORMAT_HTML,
                'inputtext' => 'Moodle is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
                'expectedtext' => 'Workplace is a popular LMS. You can download Workplace for free. MOODLE 4.2 is out now.',
            ],
            'FORMAT_HTML is allowed' => [
                'searchterm' => 'Moodle',
                'substituteterm' => 'Workplace',
                'formats' => FORMAT_HTML,
                'originalformat' => FORMAT_HTML,
                'inputtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is here.',
                'expectedtext' => '<em>Workplace</em> is a popular LMS. You can download Workplace for free. MOODLE 4.2 is here.',
            ],
            'FORMAT_HTML is not allowed' => [
                'searchterm' => 'Moodle',
                'substituteterm' => 'Workplace',
                'formats' => FORMAT_MARKDOWN . ',' . FORMAT_MOODLE,
                'originalformat' => FORMAT_HTML,
                'inputtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
                'expectedtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
            ],
            'All formats allowed - plain' => [
                'searchterm' => 'Moodle',
                'substituteterm' => 'Workplace',
                'formats' => FORMAT_HTML . ',' . FORMAT_MARKDOWN . ',' . FORMAT_MOODLE. ',' . FORMAT_PLAIN,
                'originalformat' => FORMAT_PLAIN,
                'inputtext' => 'Moodle is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
                'expectedtext' => 'Workplace is a popular LMS. You can download Workplace for free. MOODLE 4.2 is out now.',
            ],
            'FORMAT_PLAIN is allowed' => [
                'searchterm' => 'Moodle',
                'substituteterm' => 'Workplace',
                'formats' => FORMAT_PLAIN,
                'originalformat' => FORMAT_PLAIN,
                'inputtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is here.',
                'expectedtext' => '<em>Workplace</em> is a popular LMS. You can download Workplace for free. MOODLE 4.2 is here.',
            ],
            'FORMAT_PLAIN is not allowed' => [
                'searchterm' => 'Moodle',
                'substituteterm' => 'Workplace',
                'formats' => FORMAT_MARKDOWN . ',' . FORMAT_MOODLE,
                'originalformat' => FORMAT_PLAIN,
                'inputtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
                'expectedtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
            ],
            'Empty search term' => [
                'searchterm' => '',
                'substituteterm' => 'Workplace',
                'formats' => FORMAT_HTML . ',' . FORMAT_MARKDOWN . ',' . FORMAT_MOODLE. ',' . FORMAT_PLAIN,
                'originalformat' => FORMAT_HTML,
                'inputtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
                'expectedtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
            ],
            'Formats empty' => [
                'searchterm' => 'Moodle',
                'substituteterm' => 'Workplace',
                'formats' => '',
                'originalformat' => FORMAT_HTML,
                'inputtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
                'expectedtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
            ],
            'Substitute term empty' => [
                'searchterm' => 'Moodle',
                'substituteterm' => '',
                'formats' => FORMAT_HTML . ',' . FORMAT_MARKDOWN . ',' . FORMAT_MOODLE. ',' . FORMAT_PLAIN,
                'originalformat' => FORMAT_HTML,
                'inputtext' => '<em>Moodle</em> is a popular LMS. You can download Moodle for free. MOODLE 4.2 is out now.',
                'expectedtext' => '<em></em> is a popular LMS. You can download  for free. MOODLE 4.2 is out now.',
            ],
        ];
    }
}
