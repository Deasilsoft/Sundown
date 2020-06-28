<?php
/*
 * Deasilsoft/Sundown
 * Copyright (c) 2018-2020 Deasilsoft
 * https://deasilsoft.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Deasilsoft\Sundown;

class Sundown
{
    const OPTIONS = [
        "services" => [
            "twitch" => "http://player.twitch.tv/?",
            "vimeo" => "https://player.vimeo.com/video/",
            "youtube" => "https://www.youtube.com/embed/",
        ],
    ];

    const MATCH_STRING = 0x000;
    const MATCH_ORIGIN = 0x001;
    const MATCH_BOUNDARY = 0x002;
    const MATCH_RESULT = 0x003;

    private $_flags;
    private $_patterns;
    private $_formats;
    private $_options;

    /**
     * Sundown constructor.
     *
     * @param int|null $flags   Binary inclusive or flags.
     * @param array    $changes Changes to patterns, formats and options.
     */
    public function __construct ($flags = null, $changes = [])
    {
        $this->_flags = $flags;

        $this->_patterns = Regex::PATTERNS;                                                                             // load default patterns
        $this->_formats = HTML::FORMATS;                                                                                // load default formats
        $this->_options = self::OPTIONS;                                                                                // load default options

        if (isset($changes["patterns"])) foreach ($changes["patterns"] as $k => $v) $this->_patterns[$k] = $v;          // add changes to patterns
        if (isset($changes["formats"])) foreach ($changes["formats"] as $k => $v) $this->_formats[$k] = $v;             // add changes to formats
        if (isset($changes["options"])) foreach ($changes["options"] as $k => $v) $this->_options[$k] = $v;             // add changes to options
    }

    /**
     * Convert text-to-HTML.
     *
     * @param string $text Sundown formatted input.
     *
     * @return null|string HTML output.
     */
    public function Convert ($text)
    {
        if (empty($text)) return "";                                                                                    // handle empty string
        $text = preg_replace("(\\R)", "\n", $text);                                                                     // convert EOL to linux style (issues with ^ and & in regex)
        $text = $this->_ConvertBlock($text);                                                                            // convert sundown formatting
        $text = stripslashes($text);                                                                                    // strip backslashes
        return $text;
    }

    private function _HandleScript (&$match)
    {
        $language = strtolower($match[2][self::MATCH_STRING]);                                                          // make string for script language lower case; consistency for third party software
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::SCRIPT],                                                                                // format for SCRIPT
            $language,                                                                                                  // string for script language
            $match[3][self::MATCH_STRING]                                                                               // string to display in client
        );
    }

    private function _HandleQuote (&$match)
    {
        $text = preg_replace("(^> )m", "", $match[0][self::MATCH_STRING]);                                              // remove email-style quotations from string
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::QUOTE],                                                                                 // format for QUOTE
            $this->_ConvertBlock($text)                                                                                 // string to display in client
        );
    }

    private function _HandleTable (&$match)
    {
        $headers_col = false;
        $thead = null;
        $rows = preg_split("(\\R)", $match[0][self::MATCH_STRING]);                                                     // split each line into a separate table row

        foreach ($rows as &$row)
        {
            $row = preg_replace("(^\\||\\|$)", "", $row);                                                               // remove first delimiter and last delimiter (optional)
            $row = preg_split("(\\|)", $row);                                                                           // split row by cell delimiter
        }

        if (!empty($rows[1])) foreach ($rows[1] as &$cell) $headers_col = $headers_col || preg_match("(^ *=+ *$)", $cell);  // interpret the formatting for column headers
        if ($headers_col) unset($rows[1]);                                                                              // destroy row if column headers
        $headers_row = $headers_col && preg_match("(^ *=+ *$)", $rows[0][0]);                                           // interpret the formatting for row headers
        if ($headers_row) $rows[0][0] = "&nbsp;";                                                                       // empty cell if row headers

        foreach ($rows as $y => &$row)
        {
            $is_header = $y == 0 && $headers_col;                                                                       // check if cell is a header, for either column
            foreach ($row as $x => &$cell)
            {
                $is_header = $is_header || ($x == 0 && $headers_row);                                                   // check if cell is a header, for either column or row

                // TODO: colspan

                switch (substr($cell, 0, 1))                                                                            // handle first character inside cell
                {
                    case "<":
                        $cell = substr($cell, 1);                                                                       // remove first character
                        $cell = trim($cell);                                                                            // trim whitespaces
                        $cell = sprintf(
                            $this->_formats[ID::TABLE][$is_header ? "header" : "body"]["cell"]["left"],                 // format for TABLE CELL LEFT
                            1,                                                                                          // string for colspan attribute
                            $cell,                                                                                      // string to display in client
                            $y == 0 ? "col" : "row"                                                                     // string for scope attribute
                        );
                    break;
                    case ">":
                        $cell = substr($cell, 1);                                                                       // remove first character
                        $cell = trim($cell);                                                                            // trim whitespaces
                        $cell = sprintf(
                            $this->_formats[ID::TABLE][$is_header ? "header" : "body"]["cell"]["right"],                // format for TABLE CELL RIGHT
                            1,                                                                                          // string for colspan attribute
                            $cell,                                                                                      // string to display in client
                            $y == 0 ? "col" : "row"                                                                     // string for scope attribute
                        );
                    break;
                    default:
                        $cell = trim($cell);                                                                            // trim whitespaces
                        $cell = sprintf(
                            $this->_formats[ID::TABLE][$is_header ? "header" : "body"]["cell"]["center"],               // format for TABLE CELL CENTER
                            1,                                                                                          // string for colspan attribute
                            $cell,                                                                                      // string to display in client
                            $y == 0 ? "col" : "row"                                                                     // string for scope attribute
                        );
                    break;
                }
            }

            $row = sprintf(
                $this->_formats[ID::TABLE][$is_header ? "header" : "body"]["row"],                                      // format for TABLE ROW
                implode("", $row)                                                                                       // merge row
            );
        }

        if ($headers_col)                                                                                               // if column headers, define TABLE HEAD
        {
            $thead = sprintf(
                $this->_formats[ID::TABLE]["thead"],                                                                    // format for TABLE HEAD
                $rows[0]                                                                                                // string to display in client
            );
            unset($rows[0]);                                                                                            // destroy row
        }

        $tbody = sprintf(                                                                                               // put all rows that are not TABLE HEAD into TABLE BODY
            $this->_formats[ID::TABLE]["tbody"],                                                                        // format for TABLE BODY
            implode("", $rows)                                                                                          // merge rows
        );

        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::TABLE]["table"],                                                                        // format for TABLE
            $thead . $tbody                                                                                             // merge TABLE HEAD and TABLE BODY
        );
    }

    private function _HandleFigure (&$match)
    {
        $format = $this->_formats[ID::FIGURE]["default"];

        switch ($match[1][self::MATCH_STRING])
        {
            case "<":
                $format = $this->_formats[ID::FIGURE]["left"];
            break;
            case ">":
                $format = $this->_formats[ID::FIGURE]["right"];
            break;
        }

        $match[0][self::MATCH_RESULT] = sprintf(
            $format,                                                                                                    // format for IMAGE
            $match[4][self::MATCH_STRING],                                                                              // string for src attribute
            isset($match[5]) ? $match[5][self::MATCH_STRING] : null,                                                    // string for title attribute
            $match[2][self::MATCH_STRING],                                                                              // string for alt attribute
            $match[3][self::MATCH_STRING]                                                                               // string for caption
        );
    }

    private function _HandleImage (&$match)
    {
        $format = $this->_formats[ID::IMAGE]["default"];

        switch ($match[1][self::MATCH_STRING])
        {
            case "<":
                $format = $this->_formats[ID::IMAGE]["left"];
            break;
            case ">":
                $format = $this->_formats[ID::IMAGE]["right"];
            break;
        }

        $match[0][self::MATCH_RESULT] = sprintf(
            $format,                                                                                                    // format for IMAGE
            $match[3][self::MATCH_STRING],                                                                              // string for src attribute
            isset($match[4]) ? $match[4][self::MATCH_STRING] : null,                                                    // string for title attribute
            $match[2][self::MATCH_STRING]                                                                               // string for alt attribute
        );
    }

    private function _HandleFrame (&$match)
    {
        $service = strtolower($match[1][self::MATCH_STRING]);                                                           // make service string lower case
        if (key_exists($service, $this->_options["services"]) && !empty($this->_options["services"][$service]))         // check if service exists, and makes sure it's not disabled
        {
            $match[0][self::MATCH_RESULT] = sprintf(
                $this->_formats[ID::FRAME],                                                                             // format for FRAME
                $this->_options["services"][$service] . $match[2][self::MATCH_STRING]                                   // string for src attribute
            );
        }
    }

    private function _HandleDescriptionList (&$match)
    {
        $list = preg_split("((?:^|\\R)([\\-+*]{2,3}) )m", $match[0][self::MATCH_STRING], null, PREG_SPLIT_DELIM_CAPTURE);   // split up the string into a list, where every element is a list item
        array_shift($list);                                                                                             // destroy first element

        for ($i = 0; $i < count($list); $i += 2)
        {
            $text = &$list[$i + 1];                                                                                     // get list item
            $delimiter = &$list[$i];                                                                                    // get list delimiter
            $text = preg_replace("(^ +)m", "", $text);                                                                  // remove all whitespace in front
            if (substr_count($text, "\n") > 1)                                                                          // if the list item contains more than one line, process the content as blocks
            {
                $sundown = [];
                $this->_ProcessPattern(ID::PARAGRAPH, $text, $sundown);                                                 // process the block patterns we allow inside DESCRIPTION_LIST (title/description)
                switch (strlen($delimiter))
                {
                    case 3:
                        $text = sprintf(
                            $this->_formats[ID::DESCRIPTION_LIST]["title"],                                             // format for DESCRIPTION_LIST (title)
                            $this->_GetBlockResult($sundown)                                                            // get the result of the string
                        );
                    break;
                    case 2:
                        $text = sprintf(
                            $this->_formats[ID::DESCRIPTION_LIST]["item"],                                              // format for DESCRIPTION_LIST (description)
                            $this->_GetBlockResult($sundown)                                                            // get the result of the string
                        );
                    break;
                }
            }
            else switch (strlen($delimiter))                                                                            // otherwise just process the content as inline
            {
                case 3:
                    $text = sprintf(
                        $this->_formats[ID::DESCRIPTION_LIST]["title"],                                                 // format for DESCRIPTION_LIST (title)
                        $this->_ConvertInline($text)                                                                    // get the result of the string
                    );
                break;
                case 2:
                    $text = sprintf(
                        $this->_formats[ID::DESCRIPTION_LIST]["item"],                                                  // format for DESCRIPTION_LIST (description)
                        $this->_ConvertInline($text)                                                                    // get the result of the string
                    );
                break;
            }
            $delimiter = null;                                                                                          // empty delimiter
        }

        $list = array_filter($list);                                                                                    // remove all empty elements
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::DESCRIPTION_LIST]["list"],                                                              // format for DESCRIPTION_LIST (list)
            implode("", $list)                                                                                          // merge list
        );
    }

    private function _HandleOrderedList (&$match)
    {
        $this->_HandleList($match, "(^\d+\. )m", ID::ORDERED_LIST);
    }

    private function _HandleUnorderedList (&$match)
    {
        $this->_HandleList($match, "(^[\\-+*] )m", ID::UNORDERED_LIST);
    }

    private function _HandleList (&$match, $preg, $id)
    {
        $list = preg_split($preg, $match[0][self::MATCH_STRING]);                                                       // split up the string into a list, where every element is a list item
        array_shift($list);                                                                                             // destroy first element

        foreach ($list as &$item)
        {
            $text = preg_replace("(^ +)m", "", $item);                                                                  // remove all whitespace in front
            if (substr_count($text, "\n") > 1)                                                                          // if the list item contains more than one line, process the content as blocks
            {
                $sundown = [];
                $this->_ProcessPattern(ID::PARAGRAPH, $text, $sundown);                                                 // process the block patterns we allow inside UNORDERED_LIST (item)
                $item = sprintf(
                    $this->_formats[$id]["item"],                                                                       // format for UNORDERED_LIST
                    $this->_GetBlockResult($sundown)                                                                    // get the result of the string
                );
            }
            else $item = sprintf(                                                                                       // otherwise just process the content as inline
                $this->_formats[$id]["item"],                                                                           // format for UNORDERED_LIST
                $this->_ConvertInline($text)                                                                            // get the result of the string
            );
        }
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[$id]["list"],                                                                               // format for UNORDERED_LIST
            implode("", $list)                                                                                          // merge list
        );
    }

    private function _HandleNumberedHeader (&$match)
    {
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::NUMBERED_HEADER],                                                                       // format for NUMBERED_HEADER
            strlen($match[1][self::MATCH_STRING]),                                                                      // number of # (1-6)
            $match[2][self::MATCH_STRING]                                                                               // string to display in client
        );
    }

    private function _HandleUnderlinedHeader (&$match)
    {
        // switch first char on line 2
        switch ($match[2][self::MATCH_STRING])
        {
            case "=":                                                                                                   // if line 2 consists of =
                $match[0][self::MATCH_RESULT] = sprintf(
                    $this->_formats[ID::UNDERLINED_HEADER]["h1"],                                                       // format for UNDERLINED_HEADER with =
                    $match[1][self::MATCH_STRING]                                                                       // string to display in client (line 1)
                );
            break;
            case "-":                                                                                                   // if line 2 consists of -
                $match[0][self::MATCH_RESULT] = sprintf(
                    $this->_formats[ID::UNDERLINED_HEADER]["h2"],                                                       // format for UNDERLINED_HEADER with -
                    $match[1][self::MATCH_STRING]                                                                       // string to display in client (line 1)
                );
            break;
        }
    }

    private function _HandleHorizontalRule (&$match)
    {
        $match[0][self::MATCH_RESULT] = $this->_formats[ID::HORIZONTAL_RULE];                                           // HORIZONTAL_RULE only display the format
    }

    private function _HandleParagraph (&$match)
    {
        if (!ctype_space($match[0][self::MATCH_STRING]))                                                                // match isn't only whitespace
        {
            $match[0][self::MATCH_RESULT] = sprintf(
                $this->_formats[ID::PARAGRAPH],                                                                         // format for PARAGRAPH
                $this->_ConvertInline($match[0][self::MATCH_STRING])                                                    // string to display in client
            );
        }
    }

    private function _HandleKeyboard (&$match)
    {
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::KEYBOARD],                                                                              // format for KEYBOARD
            $match[2][self::MATCH_STRING]                                                                               // string to display in client
        );
    }

    private function _HandleCode (&$match)
    {
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::CODE],                                                                                  // format for CODE
            $match[2][self::MATCH_STRING]                                                                               // string to display in client
        );
    }

    private function _HandleLink (&$match)
    {
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::LINK],                                                                                  // format for LINK
            $match[2][self::MATCH_STRING],                                                                              // string for href attribute
            isset($match[3]) ? $match[3][self::MATCH_STRING] : null,                                                    // string for title attribute
            $match[1][self::MATCH_STRING]                                                                               // string to display in client
        );
    }

    // TODO: Implement references

    private function _HandleAbbreviation (&$match)
    {
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::ABBREVIATION],                                                                          // format for ABBREVIATION
            $match[2][self::MATCH_STRING],                                                                              // string for title attribute
            $match[1][self::MATCH_STRING]                                                                               // string to display in client
        );
    }

    private function _HandleSub (&$match)
    {
        $match[0][self::MATCH_RESULT] = $this->_ConvertInline(preg_replace(                                             // convert inline formatting of the resulting string
            "(^\\((.+?)\\)$)",                                                                                          // match string
            "\\1",                                                                                                      // remove paragraphs
            $match[2][self::MATCH_STRING]                                                                               // string to be formatted
        ));

        for ($i = 0; $i < strlen($match[1][self::MATCH_STRING]); $i++) $match[0][self::MATCH_RESULT] = sprintf(         // for each occurrence of the syntax, apply format
            $this->_formats[ID::SUB],                                                                                   // format for SUB
            $match[0][self::MATCH_RESULT]                                                                               // string inside previous SUB
        );
    }

    private function _HandleSup (&$match)
    {
        $match[0][self::MATCH_RESULT] = $this->_ConvertInline(preg_replace(                                             // convert inline formatting of the resulting string
            "(^\\((.+?)\\)$)",                                                                                          // match string
            "\\1",                                                                                                      // remove paragraphs
            $match[2][self::MATCH_STRING]                                                                               // string to be formatted
        ));

        for ($i = 0; $i < strlen($match[1][self::MATCH_STRING]); $i++) $match[0][self::MATCH_RESULT] = sprintf(         // for each occurrence of the syntax, apply format
            $this->_formats[ID::SUP],                                                                                   // format for SUP
            $match[0][self::MATCH_RESULT]                                                                               // string inside previous SUP
        );
    }

    private function _HandleStrikethrough (&$match)
    {
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::STRIKETHROUGH],                                                                         // format for STRIKEOUT
            $match[2][self::MATCH_STRING]                                                                               // string to be formatted
        );
    }

    private function _HandleStrong (&$match)
    {
        $text = $match[2][self::MATCH_STRING];                                                                          // string to be formatted
        $sundown = [];
        $this->_ProcessPattern(ID::EMPHASIS, $text, $sundown);                                                          // process the inline patterns we allow inside STRONG
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::STRONG],                                                                                // format for STRONG
            $this->_GetInlineResult($text, $sundown)                                                                    // get the result of the string
        );
    }

    private function _HandleEmphasis (&$match)
    {
        $text = $match[2][self::MATCH_STRING];                                                                          // string to be formatted
        $sundown = [];
        $this->_ProcessPattern(ID::STRONG, $text, $sundown);                                                            // process the inline patterns we allow inside EMPHASIS
        $match[0][self::MATCH_RESULT] = sprintf(
            $this->_formats[ID::EMPHASIS],                                                                              // format for EMPHASIS
            $this->_GetInlineResult($text, $sundown)                                                                    // get the result of the string
        );
    }

    private function _HandleLinebreak (&$match)
    {
        $match[0][self::MATCH_RESULT] = $this->_formats[ID::LINEBREAK];                                                 // LINEBREAK only display the format
    }

    private function _ProcessPattern ($id, $text, &$sundown)
    {
        preg_match_all($this->_patterns[$id], $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);                   // grab all the matches from the text
        if (empty($matches)) return;                                                                                    // exit if no matches were found
        foreach ($matches as &$match) $match[0][self::MATCH_BOUNDARY] = $match[0][self::MATCH_ORIGIN] + strlen($match[0][self::MATCH_STRING]);  // add MATCH_BOUNDARY as a value to the match

        $matches = array_filter($matches, function (&$match) use (&$sundown)                                            // filter all matches of the pattern, these are referred to as "current match"
        {
            foreach ($sundown as &$matches)                                                                             // iterates to check if current match isn't inside any previous matches
                $matches = array_filter($matches, function ($_match) use (&$match)                                      // filter all previous matches that are found to be within the current match
                {
                    if (
                        (
                            $match[0][self::MATCH_ORIGIN] >= $_match[0][self::MATCH_ORIGIN]                             // if (ORIGIN is after other ORIGIN
                            &&
                            $match[0][self::MATCH_ORIGIN] <= $_match[0][self::MATCH_BOUNDARY]                           // and before other BOUNDARY)
                        )
                        ||
                        (
                            $match[0][self::MATCH_BOUNDARY] >= $_match[0][self::MATCH_ORIGIN]                           // or if (BOUNDARY is after other ORIGIN
                            &&
                            $match[0][self::MATCH_BOUNDARY] <= $_match[0][self::MATCH_BOUNDARY]                         // and before other BOUNDARY)
                        )
                    )
                    {
                        $match = null;                                                                                  // empty current match
                    }
                    else if (
                        $match[0][self::MATCH_ORIGIN] <= $_match[0][self::MATCH_ORIGIN]                                 // if (ORIGIN is before other ORIGIN
                        &&
                        $match[0][self::MATCH_BOUNDARY] >= $_match[0][self::MATCH_BOUNDARY]                             // and BOUNDARY after other BOUNDARY)
                    )
                    {
                        return false;                                                                                   // destroy previous match, since it has a parent (I do not support prolicide, but this is an exception)
                    }

                    return $_match;
                });
            if (empty($match)) return false;                                                                            // destroy match if empty
            else return $match;
        });

        $sundown[$id] = $matches;                                                                                       // store the matches inside the sundown array
    }

    private function _ConvertBlock ($text)
    {
        $sundown = [];

        if ($this->_flags ^ Flag::NO_SCRIPTS) $this->_ProcessPattern(ID::SCRIPT, $text, $sundown);
        if ($this->_flags ^ Flag::NO_QUOTES) $this->_ProcessPattern(ID::QUOTE, $text, $sundown);
        if ($this->_flags ^ Flag::NO_TABLES) $this->_ProcessPattern(ID::TABLE, $text, $sundown);
        if ($this->_flags ^ Flag::NO_FIGURES) $this->_ProcessPattern(ID::FIGURE, $text, $sundown);
        if ($this->_flags ^ Flag::NO_IMAGES) $this->_ProcessPattern(ID::IMAGE, $text, $sundown);
        if ($this->_flags ^ Flag::NO_FRAMES) $this->_ProcessPattern(ID::FRAME, $text, $sundown);
        if ($this->_flags ^ Flag::NO_DESCRIPTION_LIST) $this->_ProcessPattern(ID::DESCRIPTION_LIST, $text, $sundown);

        $this->_ProcessPattern(ID::ORDERED_LIST, $text, $sundown);
        $this->_ProcessPattern(ID::UNORDERED_LIST, $text, $sundown);
        $this->_ProcessPattern(ID::NUMBERED_HEADER, $text, $sundown);
        $this->_ProcessPattern(ID::UNDERLINED_HEADER, $text, $sundown);
        $this->_ProcessPattern(ID::HORIZONTAL_RULE, $text, $sundown);
        $this->_ProcessPattern(ID::PARAGRAPH, $text, $sundown);

        return $this->_GetBlockResult($sundown);                                                                        // return the result of the input string
    }

    private function _ConvertInline ($text)
    {
        $sundown = [];

        if ($this->_flags ^ Flag::NO_KEYBOARD) $this->_ProcessPattern(ID::KEYBOARD, $text, $sundown);
        if ($this->_flags ^ Flag::NO_CODES) $this->_ProcessPattern(ID::CODE, $text, $sundown);
        if ($this->_flags ^ Flag::NO_LINKS) $this->_ProcessPattern(ID::LINK, $text, $sundown);
        if ($this->_flags ^ Flag::NO_ABBREVIATIONS) $this->_ProcessPattern(ID::ABBREVIATION, $text, $sundown);
        if ($this->_flags ^ Flag::NO_SUB) $this->_ProcessPattern(ID::SUB, $text, $sundown);
        if ($this->_flags ^ Flag::NO_SUP) $this->_ProcessPattern(ID::SUP, $text, $sundown);
        if ($this->_flags ^ Flag::NO_STRIKETHROUGH) $this->_ProcessPattern(ID::STRIKETHROUGH, $text, $sundown);

        $this->_ProcessPattern(ID::STRONG, $text, $sundown);
        $this->_ProcessPattern(ID::EMPHASIS, $text, $sundown);
        $this->_ProcessPattern(ID::LINEBREAK, $text, $sundown);

        return $this->_GetInlineResult($text, $sundown);                                                                // return the result of the input string
    }

    private function _GetBlockResult (&$sundown)
    {
        if (isset($sundown[ID::PARAGRAPH]))                                                                             // if the sundown contains paragraphs, make sure consecutive paragraphs are merged properly
        {
            foreach ($sundown[ID::PARAGRAPH] as &$match)
            {
                if (isset($previous_match) && ($previous_match[0][self::MATCH_BOUNDARY] + 1) == $match[0][self::MATCH_ORIGIN])      // if previous match is connected with current match
                {
                    $match[0][self::MATCH_STRING] = $previous_match[0][self::MATCH_STRING] . "\n" . $match[0][self::MATCH_STRING];  // merge previous match and current match, then empty previous match
                    $match[0][self::MATCH_ORIGIN] = $previous_match[0][self::MATCH_ORIGIN];
                    $previous_match = null;
                }
                $previous_match = &$match;                                                                              // reference current match as previous match, for next iteration
            }
            unset($previous_match, $match);                                                                             // clear reference
            $sundown[ID::PARAGRAPH] = array_filter($sundown[ID::PARAGRAPH]);                                            // destroy empty paragraphs
        }

        if (isset($sundown[ID::SCRIPT])) foreach ($sundown[ID::SCRIPT] as &$match) $this->_HandleScript($match);
        if (isset($sundown[ID::QUOTE])) foreach ($sundown[ID::QUOTE] as &$match) $this->_HandleQuote($match);
        if (isset($sundown[ID::TABLE])) foreach ($sundown[ID::TABLE] as &$match) $this->_HandleTable($match);
        if (isset($sundown[ID::FIGURE])) foreach ($sundown[ID::FIGURE] as &$match) $this->_HandleFigure($match);
        if (isset($sundown[ID::IMAGE])) foreach ($sundown[ID::IMAGE] as &$match) $this->_HandleImage($match);
        if (isset($sundown[ID::FRAME])) foreach ($sundown[ID::FRAME] as &$match) $this->_HandleFrame($match);
        if (isset($sundown[ID::ORDERED_LIST])) foreach ($sundown[ID::ORDERED_LIST] as &$match) $this->_HandleOrderedList($match);
        if (isset($sundown[ID::UNORDERED_LIST])) foreach ($sundown[ID::UNORDERED_LIST] as &$match) $this->_HandleUnorderedList($match);
        if (isset($sundown[ID::DESCRIPTION_LIST])) foreach ($sundown[ID::DESCRIPTION_LIST] as &$match) $this->_HandleDescriptionList($match);
        if (isset($sundown[ID::NUMBERED_HEADER])) foreach ($sundown[ID::NUMBERED_HEADER] as &$match) $this->_HandleNumberedHeader($match);
        if (isset($sundown[ID::UNDERLINED_HEADER])) foreach ($sundown[ID::UNDERLINED_HEADER] as &$match) $this->_HandleUnderlinedHeader($match);
        if (isset($sundown[ID::HORIZONTAL_RULE])) foreach ($sundown[ID::HORIZONTAL_RULE] as &$match) $this->_HandleHorizontalRule($match);
        if (isset($sundown[ID::PARAGRAPH])) foreach ($sundown[ID::PARAGRAPH] as &$match) $this->_HandleParagraph($match);

        $this->_DestroyEmpty($sundown);                                                                                 // destroy empty matches
        $text = null;                                                                                                   // make empty string
        foreach ($this->_SortMatches($sundown) as $match) $text .= $match[self::MATCH_RESULT];                          // append each consecutive block
        return $text;
    }

    private function _GetInlineResult ($text, &$sundown)
    {
        if (isset($sundown[ID::KEYBOARD])) foreach ($sundown[ID::KEYBOARD] as &$match) $this->_HandleKeyboard($match);
        if (isset($sundown[ID::CODE])) foreach ($sundown[ID::CODE] as &$match) $this->_HandleCode($match);
        if (isset($sundown[ID::LINK])) foreach ($sundown[ID::LINK] as &$match) $this->_HandleLink($match);
        if (isset($sundown[ID::ABBREVIATION])) foreach ($sundown[ID::ABBREVIATION] as &$match) $this->_HandleAbbreviation($match);
        if (isset($sundown[ID::SUB])) foreach ($sundown[ID::SUB] as &$match) $this->_HandleSub($match);
        if (isset($sundown[ID::SUP])) foreach ($sundown[ID::SUP] as &$match) $this->_HandleSup($match);
        if (isset($sundown[ID::STRONG])) foreach ($sundown[ID::STRONG] as &$match) $this->_HandleStrong($match);
        if (isset($sundown[ID::EMPHASIS])) foreach ($sundown[ID::EMPHASIS] as &$match) $this->_HandleEmphasis($match);
        if (isset($sundown[ID::STRIKETHROUGH])) foreach ($sundown[ID::STRIKETHROUGH] as &$match) $this->_HandleStrikethrough($match);
        if (isset($sundown[ID::LINEBREAK])) foreach ($sundown[ID::LINEBREAK] as &$match) $this->_HandleLinebreak($match);
        if (isset($sundown[ID::LINEBREAK])) foreach ($sundown[ID::LINEBREAK] as &$match) $this->_HandleLinebreak($match);

        $this->_DestroyEmpty($sundown);                                                                                 // destroy empty matches

        foreach ($this->_SortMatches($sundown, true) as $match) $text = substr_replace(                                 // SortMatches() has to run reverse in order to correctly insert the replacements
            $text,                                                                                                      // subject string, the string we make changes to
            $match[self::MATCH_RESULT],                                                                                 // replacement string to insert into subject string
            $match[self::MATCH_ORIGIN],                                                                                 // the origin point, where we start our replacement
            $match[self::MATCH_BOUNDARY] - $match[self::MATCH_ORIGIN]                                                   // the string length of our original string
        );

        return $text;
    }

    private function _DestroyEmpty (&$sundown)
    {
        foreach ($sundown as &$matches) $matches = array_filter($matches, function (&$match)                            // go trough every set of matches and filter out empty matches
        {
            if (!isset($match[0][self::MATCH_RESULT])) return false;                                                    // destroy matches with no result
            return $match;
        });
    }

    private function _SortMatches (&$sundown, $reverse = false)
    {
        $sorted_matches = [];
        foreach ($sundown as &$matches) foreach ($matches as &$match) array_push($sorted_matches, $match[0]);           // collect all the relevant data into one array
        usort($sorted_matches, function ($lhs, $rhs) use ($reverse)                                                     // sort the array beginning with the first match, or last match if $reverse = true
        {
            if ($reverse) return $lhs[self::MATCH_ORIGIN] < $rhs[self::MATCH_ORIGIN];                                   // sort ascending
            else return $lhs[self::MATCH_ORIGIN] > $rhs[self::MATCH_ORIGIN];                                            // sort descending
        });
        return $sorted_matches;
    }
}
