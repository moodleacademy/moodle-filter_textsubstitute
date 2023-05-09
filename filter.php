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

/**
 * Filter to substitute text.
 *
 * @package     filter_textsubstitute
 * @category    admin
 * @copyright   2023 Your Name <you@example.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class filter_textsubstitute extends moodle_text_filter {
    /**
     * Apply the filter to the text
     *
     * @see filter_manager::apply_filter_chain()
     * @param string $text to be processed by the text
     * @param array $options filter options
     * @return string text after processing
     */
    public function filter($text, array $options = []) {
        $searchterm = get_config('filter_textsubstitute', 'searchterm');

        // If the format is not specified or search term is empty, we do nothing.
        if (!isset($options['originalformat']) || empty($searchterm)) {
            return $text;
        }

        if (in_array($options['originalformat'], explode(',', get_config('filter_textsubstitute', 'formats')))) {
            $replacewith = get_config('filter_textsubstitute', 'substituteterm');

            // Return the modified text.
            return $this->substitute_term($text, $searchterm, $replacewith);
        }

        return $text;
    }

    /**
     * Substitute a term with another.
     *
     * @param string $text - text to modify
     * @param string $searchterm - term to replace
     * @param string $replacewith - term to substitute with
     * @return string the modified result
     */
    protected function substitute_term($text, string $searchterm, string $replacewith) {
        $text = str_replace($searchterm, $replacewith, $text);
        return $text;
    }
}
