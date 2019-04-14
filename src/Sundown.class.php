<?php

/*
 * Deasilsoft/Sundown
 * < http://deasilsoft.com >
 *
 * Copyright (c) 2018 Sondre Benjamin Aasen
 * < sondreaasen@hotmail.com >
 *
 * Inspired by John Gruber's Markdown
 * < http://daringfireball.net >
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

namespace Deasilsoft;

class Sundown {

    /*
     * FLAGS
     */

    const FLAG_NO_SCRIPTS = 1 << 0x000;
    const FLAG_NO_CODES = 1 << 0x001;
    const FLAG_NO_QUOTES = 1 << 0x002;
    const FLAG_NO_TABLES = 1 << 0x003;
    const FLAG_NO_FIGURES = 1 << 0x004;
    const FLAG_NO_IMAGES = 1 << 0x005;
    const FLAG_NO_FRAMES = 1 << 0x006;
    const FLAG_NO_DESCRIPTION_LIST = 1 << 0x007;
    const FLAG_NO_KEYBOARD = 1 << 0x008;
    const FLAG_NO_LINKS = 1 << 0x009;
    const FLAG_NO_ABBREVIATIONS = 1 << 0x00A;
    const FLAG_NO_SUB = 1 << 0x00B;
    const FLAG_NO_SUP = 1 << 0x00C;
    const FLAG_NO_STRIKETHROUGH = 1 << 0x00D;

    /*
     * BLOCK IDENTIFIERS
     */

    const ID_SCRIPT = 0x000;
    const ID_QUOTE = 0x001;
    const ID_TABLE = 0x002;
    const ID_FIGURE = 0x003;
    const ID_IMAGE = 0x004;
    const ID_FRAME = 0x005;
    const ID_DESCRIPTION_LIST = 0x006;
    const ID_ORDERED_LIST = 0x007;
    const ID_UNORDERED_LIST = 0x008;
    const ID_NUMBERED_HEADER = 0x009;
    const ID_UNDERLINED_HEADER = 0x00A;
    const ID_HORIZONTAL_RULE = 0x00B;
    const ID_PARAGRAPH = 0x00C;

    /*
     * INLINE IDENTIFIERS
     */

    const ID_CODE = 0xF00;
    const ID_KEYBOARD = 0xF01;
    const ID_LINK = 0xF02;
    const ID_ABBREVIATION = 0xF03;
    const ID_SUB = 0xF04;
    const ID_SUP = 0xF05;
    const ID_STRIKETHROUGH = 0xF06;
    const ID_STRONG = 0xF07;
    const ID_EMPHASIS = 0xF08;
    const ID_LINEBREAK = 0xF09;

    const PATTERNS = [

        /*
         * BLOCK PATTERNS
         */

        self::ID_SCRIPT => "
        (                                                               # [0]
            ^                                                           # line begin
            (```)                                                       # [1] match begin
            (?:                                                         # optional begin
                \\ (.+?)                                                # [2] match language (optional)
            )?                                                          # optional end
            \\R                                                         # line new
            (                                                           # [3] match content begin
                (?:                                                     # [3] repeatable loop begin
                    .*?                                                 # [3] match content
                    \\R                                                 # [3] line new
                )+                                                      # [3] repeatable loop end
            )                                                           # [3] match content end
            \\1                                                         # match end
            $                                                           # line end
        )mx",

        self::ID_QUOTE => "
        (                                                               # [0]
            ^                                                           # line begin
            >\\ .+?                                                     # match content (first line)
            (?:                                                         # repeatable start
                \\R                                                     # line new
                >\\ .+?                                                 # match content
            )*                                                          # repeatable end
            $                                                           # line end
        )mx",

        self::ID_TABLE => "
        (                                                               # [0]
            ^                                                           # line begin
            (?:                                                         # repeatable start
                \\|                                                     # match syntax
                [<>]?                                                   # match alignment
                .+?                                                     # match content
            )+                                                          # repeatable end
            \\|?                                                        # optional syntax
            (?:                                                         # repeatable start
                \\R                                                     # line new
                (?:                                                     # repeatable start
                    \\|                                                 # match syntax
                    [<>]?                                               # match alignment
                    .+?                                                 # match content
                )+                                                      # repeatable end
                \\|?                                                    # optional syntax
            )+                                                          # repeatable end
            $                                                           # line end
        )mx",

        self::ID_FIGURE => "
        (                                                               # [0]
            ^                                                           # line begin
            \\!                                                         # match syntax
            (<|>)?                                                      # [1] match alignment
            \\[                                                         # match syntax
                (.+?)                                                   # [2] match alt attribute
            \\]\\[                                                      # match syntax
                (.+?)                                                   # [3] match caption
            \\]\\(                                                      # match syntax
                (.+?)                                                   # [4] match src attribute
                (?:                                                     # optional begin
                    \\ \"(.+?)\"                                        # [5] match title attribute (optional)
                )?                                                      # optional end
            \\)                                                         # match syntax
            $                                                           # line end
        )mx",

        self::ID_IMAGE => "
        (                                                               # [0]
            ^                                                           # line begin
            \\!                                                         # match syntax
            (<|>)?                                                      # [1] match alignment
            \\[                                                         # match syntax
                (.+?)                                                   # [2] match alt attribute
            \\]\\(                                                      # match syntax
                (.+?)                                                   # [3] match src attribute
                (?:                                                     # optional begin
                    \\ \"(.+?)\"                                        # [4] match title attribute (optional)
                )?                                                      # optional end
            \\)                                                         # match syntax
            $                                                           # line end
        )mx",

        self::ID_FRAME => "
        (                                                               # [0]
            ^                                                           # line begin
            \\[\\[                                                      # match syntax
                (.+?)                                                   # [1] match service
            \\]\\]                                                      # match syntax
            \\(                                                         # optional syntax
                (.+?)                                                   # [2] match service destination
            \\)                                                         # optional syntax
            $                                                           # line end
        )mx",

        self::ID_DESCRIPTION_LIST => "
        (                                                               # [0]
            ^                                                           # line begin
            ([\\-+*])\\1{1,2}\\                                         # [1] match list delimiter
            .+?                                                         # match content (first line)
            (?:                                                         # repeatable begin
                \\R+                                                    # line new
                (?:\\ {1,4}|\\t)                                        # match syntax
                .+?                                                     # match content
            )*                                                          # repeatable end
            (?:                                                         # repeatable begin
                \\R                                                     # line new
                ([\\-+*])\\2{1,2}\\                                     # [2] match list delimiter
                .+?                                                     # match content
                (?:                                                     # repeatable begin
                    \\R+                                                # line new
                    (?:\\ {1,4}|\\t)                                    # match syntax
                    .+?                                                 # match content
                )*                                                      # repeatable end
            )*                                                          # repeatable end
            $                                                           # line end
        )mx",

        self::ID_ORDERED_LIST => "
        (                                                               # [0]
            ^                                                           # line begin
            \\d+\\.\\                                                   # match list delimiter
            .+?                                                         # match content (first line)
            (?:                                                         # repeatable begin
                \\R+                                                    # line new
                (?:\\ {1,4}|\\t)                                        # match syntax
                .+?                                                     # match content
            )*                                                          # repeatable end
            (?:                                                         # repeatable begin
                \\R                                                     # line new
                \\d+\\.\\                                               # match list delimiter
                .+?                                                     # match content
                (?:                                                     # repeatable begin
                    \\R+                                                # line new
                    (?:\\ {1,4}|\\t)                                    # match syntax
                    .+?                                                 # match content
                )*                                                      # repeatable end
            )*                                                          # repeatable end
            $                                                           # line end
        )mx",

        self::ID_UNORDERED_LIST => "
        (                                                               # [0]
            ^                                                           # line begin
            [\\-+*]\\                                                   # match list delimiter
            .+?                                                         # match content (first line)
            (?:                                                         # repeatable begin
                \\R+                                                    # line new
                (?:\\ {1,4}|\\t)                                        # match syntax
                .+?                                                     # match content
            )*                                                          # repeatable end
            (?:                                                         # repeatable begin
                \\R                                                     # line new
                [\\-+*]\\                                               # match list delimiter
                .+?                                                     # match content
                (?:                                                     # repeatable begin
                    \\R+                                                # line new
                    (?:\\ {1,4}|\\t)                                    # match syntax
                    .+?                                                 # match content
                )*                                                      # repeatable end
            )*                                                          # repeatable end
            $                                                           # line end
        )mx",

        self::ID_NUMBERED_HEADER => "
        (                                                               # [0]
            ^                                                           # line begin
            (\\#{1,6})\\                                                # [1] match syntax
            (.+?)                                                       # [2] match content
            \\1?                                                        # optional syntax
            $                                                           # line end
        )mx",

        self::ID_UNDERLINED_HEADER => "
        (                                                               # [0]
            ^                                                           # line begin
            (.+?)                                                       # [1] match content
            \\R                                                         # line new
            (-|=)\\2{2,}                                                # [2] match syntax
            $                                                           # line end
        )mx",

        self::ID_HORIZONTAL_RULE => "
        (                                                               # [0]
            ^                                                           # line begin
            ([\\-=*~_])\\1{2,}                                          # [1] match syntax
            $                                                           # line end
        )mx",

        self::ID_PARAGRAPH => "
        (                                                               # [0]
            ^                                                           # line begin
            .+?                                                         # match content
            $                                                           # line end
        )mx",

        /*
         * INLINE PATTERNS
         */

        self::ID_KEYBOARD => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (``)                                                        # [1] match begin
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",

        self::ID_CODE => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (`)                                                         # [1] match begin
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",

        self::ID_LINK => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            \\[                                                         # match syntax
                (.+?)                                                   # [1] match content
            \\]\\(                                                      # match syntax
                (.+?)                                                   # [2] match href attribute
                (?:                                                     # optional begin
                    \\ \"(.+?)\"                                        # [3] match title attribute (optional)
                )?                                                      # optional end
            \\)                                                         # match syntax
        )mx",

        self::ID_ABBREVIATION => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            \\{                                                         # match syntax
                (.+?)                                                   # [1] match content
            \\}\\(                                                      # match syntax
                (.+?)                                                   # [2] match title
            \\)                                                         # match syntax
        )mx",

        self::ID_SUB => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            !                                                           # match syntax
            (\\^+)                                                      # [1] match syntax
            (                                                           # [2] match begin
                (?<!\\\\)                                               # [2] match escape
                \\(                                                     # [2] match syntax
                .+?                                                     # [2] match content
                (?<!\\\\)                                               # [2] match escape
                \\)                                                     # [2] match syntax
                |                                                       # [2] else
                [^ ]+                                                   # [2] match content
            )                                                           # [2] match end
        )mx",

        self::ID_SUP => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (\\^+)                                                      # [1] match syntax
            (                                                           # [2] match begin
                (?<!\\\\)                                               # [2] match escape
                \\(                                                     # [2] match syntax
                .+?                                                     # [2] match content
                (?<!\\\\)                                               # [2] match escape
                \\)                                                     # [2] match syntax
                |                                                       # [2] else
                [^ ]+                                                   # [2] match content
            )                                                           # [2] match end
        )mx",

        self::ID_STRIKETHROUGH => "
        (
            (?<!\\\\)                                                   # match escape
            (~~)                                                        # [1] match start
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",

        self::ID_STRONG => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (__|\\*\\*)                                                 # [1] match start
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",

        self::ID_EMPHASIS => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (_|\\*)                                                     # [1] match start
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",

        self::ID_LINEBREAK => "
        (                                                               # [0]
            (?<!^)                                                      # match line begin (NO BLANK LINES)
            (?<!\\\\)                                                   # match escape
            \\ {2,}                                                     # match syntax
            $                                                           # line end
        )mx",

    ];

    const FORMATS = [

        /*
         * BLOCK FORMATS
         */

        self::ID_SCRIPT => /** @lang text */
            "<pre><code class='language-%1\$s' data-language='%1\$s'>\n%2\$s</code></pre>\n",

        self::ID_QUOTE => /** @lang text */
            "<blockquote class='blockquote'>\n%s</blockquote>\n",

        self::ID_TABLE => [

            "table" => /** @lang text */
                "<table class='table'>\n%s</table>\n",

            "thead" => /** @lang text */
                "<thead>\n%s</thead>\n",

            "tbody" => /** @lang text */
                "<tbody>\n%s</tbody>\n",

            "tr" => /** @lang text */
                "<tr>\n%s</tr>\n",

            "th" => [

                "left" => /** @lang text */
                    "<th class='text-left' colspan='%1\$s' scope='%3\$s'>\n%2\$s\n</th>\n",

                "center" => /** @lang text */
                    "<th class='text-center' colspan='%1\$s' scope='%3\$s'>\n%2\$s\n</th>\n",

                "right" => /** @lang text */
                    "<th class='text-right' colspan='%1\$s' scope='%3\$s'>\n%2\$s\n</th>\n",

            ],

            "td" => [

                "left" => /** @lang text */
                    "<td class='text-left' colspan='%1\$s'>\n%2\$s\n</td>\n",

                "center" => /** @lang text */
                    "<td class='text-center' colspan='%1\$s'>\n%2\$s\n</td>\n",

                "right" => /** @lang text */
                    "<td class='text-right' colspan='%1\$s'>\n%2\$s\n</td>\n",

            ],

        ],

        self::ID_FIGURE => [

            "default" => /** @lang text */
                "<figure><img src='%s' class='float-none' title='%s' alt='%s'><figcaption>%s</figcaption></figure>\n",

            "left" => /** @lang text */
                "<figure><img src='%s' class='float-left' title='%s' alt='%s'><figcaption>%s</figcaption></figure>\n",

            "right" => /** @lang text */
                "<figure><img src='%s' class='float-right' title='%s' alt='%s'><figcaption>%s</figcaption></figure>\n",

        ],

        self::ID_IMAGE => [

            "default" => /** @lang text */
                "<img src='%s' class='float-none' title='%s' alt='%s'>\n",

            "left" => /** @lang text */
                "<img src='%s' class='float-left' title='%s' alt='%s'>\n",

            "right" => /** @lang text */
                "<img src='%s' class='float-right' title='%s' alt='%s'>\n",

        ],

        self::ID_FRAME => /** @lang text */
            "<iframe src='%s' width='%s' height='%s' frameborder='0' allowfullscreen></iframe>\n",

        self::ID_ORDERED_LIST => [

            "ol" => /** @lang text */
                "<ol>\n%s</ol>\n",

            "li" => /** @lang text */
                "<li>\n%s</li>\n",

        ],

        self::ID_UNORDERED_LIST => [

            "ul" => /** @lang text */
                "<ul>\n%s</ul>\n",

            "li" => /** @lang text */
                "<li>\n%s</li>\n",

        ],

        self::ID_DESCRIPTION_LIST => [

            "dl" => /** @lang text */
                "<dl>\n%s</dl>\n",

            "dt" => /** @lang text */
                "<dt>\n%s\n</dt>\n",

            "dd" => /** @lang text */
                "<dd>\n%s\n</dd>\n",

        ],

        self::ID_NUMBERED_HEADER => /** @lang text */
            "<h%1\$d>%2\$s</h%1\$d>\n",

        self::ID_UNDERLINED_HEADER => [

            "h1" => /** @lang text */
                "<h1 class='display-2'>%s</h1>\n",

            "h2" => /** @lang text */
                "<h2 class='display-4'>%s</h2>\n",

        ],

        self::ID_HORIZONTAL_RULE => /** @lang text */
            "<hr>\n",

        self::ID_PARAGRAPH => /** @lang text */
            "<p>\n%s\n</p>\n",

        /*
         * INLINE FORMATS
         */

        self::ID_KEYBOARD => /** @lang text */
            "<kbd>%s</kbd>",

        self::ID_CODE => /** @lang text */
            "<code>%s</code>",

        self::ID_LINK => /** @lang text */
            "<a href='%s' title='%s'>%s</a>",

        self::ID_ABBREVIATION => /** @lang text */
            "<abbr title='%s'>%s</abbr>",

        self::ID_SUB => /** @lang text */
            "<sub>%s</sub>",

        self::ID_SUP => /** @lang text */
            "<sup>%s</sup>",

        self::ID_STRIKETHROUGH => /** @lang text */
            "<s>%s</s>",

        self::ID_STRONG => /** @lang text */
            "<strong>%s</strong>",

        self::ID_EMPHASIS => /** @lang text */
            "<em>%s</em>",

        self::ID_LINEBREAK => /** @lang text */
            "<br>",

    ];

    /*
     * OPTIONS
     */

    const OPTIONS = [

        "frame-width" => "854px",
        "frame-height" => "480px",

        "services" => [

            "twitch" => "http://player.twitch.tv/?",
            "vimeo" => "https://player.vimeo.com/video/",
            "youtube" => "https://www.youtube.com/embed/",

        ],

    ];

    /*
     * MATCH HELPERS
     */

    const MATCH_STRING = 0x000;
    const MATCH_ORIGIN = 0x001;
    const MATCH_BOUNDARY = 0x002;
    const MATCH_RESULT = 0x003;

    /*
     * VARIABLES
     */

    private $flags;
    private $patterns;
    private $formats;
    private $options;

    /*
     * PUBLIC FUNCTIONS
     */

    /**
     * Sundown constructor.
     * @param int|null $flags Binary inclusive or flags.
     * @param array $changes Changes to patterns, formats and options.
     */
    public function __construct($flags = null, $changes = []) {

        $this->flags = $flags;

        // load all default values
        $this->patterns = static::PATTERNS;
        $this->formats = static::FORMATS;
        $this->options = static::OPTIONS;

        // update patterns, formats and options to the values specified by $changes
        if (isset($changes["patterns"])) foreach ($changes["patterns"] as $key => $value) $this->patterns[$key] = $value;
        if (isset($changes["formats"])) foreach ($changes["formats"] as $key => $value) $this->formats[$key] = $value;
        if (isset($changes["options"])) foreach ($changes["options"] as $key => $value) $this->options[$key] = $value;

    }

    /**
     * Convert text-to-HTML.
     * @param string $text Sundown formatted input.
     * @return null|string HTML output.
     */
    public function convert($text) {

        if (empty($text)) return null;                                  // handle empty string
        $text = preg_replace("(\\R)", "\n", $text);                     // convert EOL to linux style (issues with ^ and & in regex)
        $text = $this->_convert_block($text);                           // convert sundown formatting
        $text = stripslashes($text);                                    // strip backslashes

        return $text;

    }

    /*
     * PRIVATE FUNCTIONS
     */

    private function _handle_script(&$match) {

        // make string for script language lower case; this is used by thirdparty software
        $language = strtolower($match[2][static::MATCH_STRING]);

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_SCRIPT],                          // format for SCRIPT
            $language,                                                  // string for script language
            $match[3][static::MATCH_STRING]                             // string to display in client
        );

    }

    private function _handle_quote(&$match) {

        // remove epost-style quotations from string
        $text = preg_replace("(^> )m", "", $match[0][static::MATCH_STRING]);

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_QUOTE],                           // format for QUOTE
            $this->_convert_block($text)                                // string to display in client
        );

    }

    private function _handle_table(&$match) {

        // declare variables
        $headers_col = false;
        $thead = null;

        // split each line into a separate table row
        $rows = preg_split("(\\R)", $match[0][static::MATCH_STRING]);

        foreach ($rows as &$row) {

            $row = preg_replace("(^\\||\\|$)", "", $row);               // remove first delimiter and last delimiter (optional)
            $row = preg_split("(\\|)", $row);                           // split row by cell delimiter

        }

        // interpret the formatting for column headers
        if (!empty($rows[1])) foreach ($rows[1] as &$cell) $headers_col = $headers_col || preg_match("(^ *=+ *$)", $cell);

        // destroy row if column headers
        if ($headers_col) unset($rows[1]);

        // interpret the formatting for row headers
        $headers_row = $headers_col && preg_match("(^ *=+ *$)", $rows[0][0]);

        // empty cell if row headers
        if ($headers_row) $rows[0][0] = "&nbsp;";

        foreach ($rows as $y => &$row) {

            foreach ($row as $x => &$cell) {

                // check if cell is a header, for either column or row
                $is_header = ($y == 0 && $headers_col) || ($x == 0 && $headers_row);

                // TODO: colspan

                // handle first character inside cell
                switch (substr($cell, 0, 1)) {

                    case "<":

                        $cell = substr($cell, 1);                       // remove first character
                        $cell = trim($cell);                            // trim whitespaces
                        $cell = sprintf(
                            $this->formats[static::ID_TABLE][$is_header ? "th" : "td"]["left"], // format for TABLE CELL LEFT
                            1,                                          // string for colspan attribute
                            $cell,                                      // string to display in client
                            $y == 0 ? "col" : "row"                     // string for scope attribute
                        );

                        break;

                    case ">":

                        $cell = substr($cell, 1);                       // remove first character
                        $cell = trim($cell);                            // trim whitespaces
                        $cell = sprintf(
                            $this->formats[static::ID_TABLE][$is_header ? "th" : "td"]["right"], // format for TABLE CELL RIGHT
                            1,                                          // string for colspan attribute
                            $cell,                                      // string to display in client
                            $y == 0 ? "col" : "row"                     // string for scope attribute
                        );

                        break;

                    default:

                        $cell = trim($cell);                            // trim whitespaces
                        $cell = sprintf(
                            $this->formats[static::ID_TABLE][$is_header ? "th" : "td"]["center"], // format for TABLE CELL CENTER
                            1,                                          // string for colspan attribute
                            $cell,                                      // string to display in client
                            $y == 0 ? "col" : "row"                     // string for scope attribute
                        );

                        break;

                }

            }

            $row = sprintf(
                $this->formats[static::ID_TABLE]["tr"],                 // format for TABLE ROW
                implode("", $row)                                       // merge row
            );

        }

        // if column headers, define TABLE HEAD
        if ($headers_col) {

            $thead = sprintf(
                $this->formats[static::ID_TABLE]["thead"],              // format for TABLE HEAD
                $rows[0]                                                // string to display in client
            );

            // destroy row
            unset($rows[0]);

        }

        // put all rows that are not TABLE HEAD into TABLE BODY
        $tbody = sprintf(
            $this->formats[static::ID_TABLE]["tbody"],                  // format for TABLE BODY
            implode("", $rows)                                          // merge rows
        );

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_TABLE]["table"],                  // format for TABLE
            $thead . $tbody                                             // merge TABLE HEAD and TABLE BODY
        );

    }

    private function _handle_figure(&$match) {

        $format = $this->formats[static::ID_FIGURE]["default"];

        switch ($match[1][static::MATCH_STRING]) {

            case "<":

                $format = $this->formats[static::ID_FIGURE]["left"];

                break;

            case ">":

                $format = $this->formats[static::ID_FIGURE]["right"];

                break;

        }

        $match[0][static::MATCH_RESULT] = sprintf(
            $format,                                                    // format for IMAGE
            $match[4][static::MATCH_STRING],                            // string for src attribute
            isset($match[5]) ? $match[5][static::MATCH_STRING] : null,  // string for title attribute
            $match[2][static::MATCH_STRING],                            // string for alt attribute
            $match[3][static::MATCH_STRING]                             // string for caption
        );

    }

    private function _handle_image(&$match) {

        $format = $this->formats[static::ID_IMAGE]["default"];

        switch ($match[1][static::MATCH_STRING]) {

            case "<":

                $format = $this->formats[static::ID_IMAGE]["left"];

                break;

            case ">":

                $format = $this->formats[static::ID_IMAGE]["right"];

                break;

        }

        $match[0][static::MATCH_RESULT] = sprintf(
            $format,                                                    // format for IMAGE
            $match[3][static::MATCH_STRING],                            // string for src attribute
            isset($match[4]) ? $match[4][static::MATCH_STRING] : null,  // string for title attribute
            $match[2][static::MATCH_STRING]                             // string for alt attribute
        );

    }

    private function _handle_frame(&$match) {

        $service = strtolower($match[1][static::MATCH_STRING]);         // make service string lower case

        // check if service exists, and makes sure it's not disabled
        if (key_exists($service, $this->options["services"]) && !empty($this->options["services"][$service])) {

            $match[0][static::MATCH_RESULT] = sprintf(
                $this->formats[static::ID_FRAME],                       // format for FRAME
                $this->options["services"][$service] . $match[2][static::MATCH_STRING], // string for src attribute
                $this->options["frame-width"],                          // string for width attribute
                $this->options["frame-height"]                          // string for height attribute
            );

        }

    }

    private function _handle_description_list(&$match) {

        // split up the string into a list, where every element is a list item
        $list = preg_split("((?:^|\\R)([\\-+*]{2,3}) )m", $match[0][static::MATCH_STRING], null, PREG_SPLIT_DELIM_CAPTURE);
        array_shift($list); // destroy first element

        for ($i = 0; $i < count($list); $i += 2) {

            // get list item
            $text = &$list[$i + 1];

            // get list delimiter
            $delimiter = &$list[$i];

            // remove all whitespace in front
            $text = preg_replace("(^ +)m", "", $text);

            // if the list item contains more than one line, process the content as blocks
            // otherwise just process the content as inline
            if (substr_count($text, "\n") > 1) {

                $sundown = [];

                // process the block patterns we allow inside DESCRIPTION_LIST (title/description)
                $this->_process_pattern(static::ID_PARAGRAPH, $text, $sundown);

                switch (strlen($delimiter)) {

                    case 3:

                        $text = sprintf(
                            $this->formats[static::ID_DESCRIPTION_LIST]["dt"],  // format for DESCRIPTION_LIST (title)
                            $this->_get_block_result($sundown)                  // get the result of the string
                        );

                        break;

                    case 2:

                        $text = sprintf(
                            $this->formats[static::ID_DESCRIPTION_LIST]["dd"],  // format for DESCRIPTION_LIST (description)
                            $this->_get_block_result($sundown)                  // get the result of the string
                        );

                        break;

                }

            } else switch (strlen($delimiter)) {

                case 3:

                    $text = sprintf(
                        $this->formats[static::ID_DESCRIPTION_LIST]["dt"],      // format for DESCRIPTION_LIST (title)
                        $this->_convert_inline($text)                           // get the result of the string
                    );

                    break;

                case 2:

                    $text = sprintf(
                        $this->formats[static::ID_DESCRIPTION_LIST]["dd"],      // format for DESCRIPTION_LIST (description)
                        $this->_convert_inline($text)                           // get the result of the string
                    );

                    break;

            }

            // empty delimiter
            $delimiter = null;

        }

        // remove all empty elements
        $list = array_filter($list);

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_DESCRIPTION_LIST]["dl"],          // format for DESCRIPTION_LIST (list)
            implode("", $list)                                          // merge list
        );

    }

    private function _handle_ordered_list(&$match) {

        // split up the string into a list, where every element is a list item
        $list = preg_split("(^\d+\. )m", $match[0][static::MATCH_STRING]);
        array_shift($list);

        foreach ($list as &$item) {

            // remove all whitespace in front
            $text = preg_replace("(^ +)m", "", $item);

            // if the list item contains more than one line, process the content as blocks
            // otherwise just process the content as inline
            if (substr_count($text, "\n") > 1) {

                $sundown = [];

                // process the block patterns we allow inside ORDERED_LIST (li)
                $this->_process_pattern(static::ID_PARAGRAPH, $text, $sundown);

                $item = sprintf(
                    $this->formats[static::ID_ORDERED_LIST]["li"],      // format for ORDERED_LIST (li)
                    $this->_get_block_result($sundown)                  // get the result of the string
                );

            } else $item = sprintf(
                $this->formats[static::ID_ORDERED_LIST]["li"],          // format for ORDERED_LIST (li)
                $this->_convert_inline($text)                           // get the result of the string
            );

        }

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_ORDERED_LIST]["ol"],              // format for ORDERED_LIST (ol)
            implode("", $list)                                          // merge list
        );

    }

    private function _handle_unordered_list(&$match) {

        // split up the string into a list, where every element is a list item
        $list = preg_split("(^[\\-+*] )m", $match[0][static::MATCH_STRING]);
        array_shift($list); // destroy first element

        foreach ($list as &$item) {

            // remove all whitespace in front
            $text = preg_replace("(^ +)m", "", $item);

            // if the list item contains more than one line, process the content as blocks
            // otherwise just process the content as inline
            if (substr_count($text, "\n") > 1) {

                $sundown = [];

                // process the block patterns we allow inside UNORDERED_LIST (item)
                $this->_process_pattern(static::ID_PARAGRAPH, $text, $sundown);

                $item = sprintf(
                    $this->formats[static::ID_UNORDERED_LIST]["li"],    // format for UNORDERED_LIST (li)
                    $this->_get_block_result($sundown)                  // get the result of the string
                );

            } else $item = sprintf(
                $this->formats[static::ID_UNORDERED_LIST]["li"],        // format for UNORDERED_LIST (li)
                $this->_convert_inline($text)                           // get the result of the string
            );

        }

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_UNORDERED_LIST]["ul"],            // format for UNORDERED_LIST (ol)
            implode("", $list)                                          // merge list
        );

    }

    private function _handle_numbered_header(&$match) {

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_NUMBERED_HEADER],                 // format for NUMBERED_HEADER
            strlen($match[1][static::MATCH_STRING]),                    // number of # (1-6)
            $match[2][static::MATCH_STRING]                             // string to display in client
        );

    }

    private function _handle_underlined_header(&$match) {

        // switch first char on line 2
        switch ($match[2][static::MATCH_STRING]) {

            case "=":                                                   // if line 2 consists of =

                $match[0][static::MATCH_RESULT] = sprintf(
                    $this->formats[static::ID_UNDERLINED_HEADER]["h1"], // format for UNDERLINED_HEADER with =
                    $match[1][static::MATCH_STRING]                     // string to display in client (line 1)
                );

                break;

            case "-":                                                   // if line 2 consists of -

                $match[0][static::MATCH_RESULT] = sprintf(
                    $this->formats[static::ID_UNDERLINED_HEADER]["h2"], // format for UNDERLINED_HEADER with -
                    $match[1][static::MATCH_STRING]                     // string to display in client (line 1)
                );

                break;

        }

    }

    private function _handle_horizontal_rule(&$match) {

        // HORIZONTAL_RULE only display the format
        $match[0][static::MATCH_RESULT] = $this->formats[static::ID_HORIZONTAL_RULE];

    }

    private function _handle_paragraph(&$match) {

        if (!ctype_space($match[0][static::MATCH_STRING])) {            // match isn't only whitespace

            $match[0][static::MATCH_RESULT] = sprintf(
                $this->formats[static::ID_PARAGRAPH],                   // format for PARAGRAPH
                $this->_convert_inline($match[0][static::MATCH_STRING]) // string to display in client
            );

        }

    }

    private function _handle_keyboard(&$match) {

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_KEYBOARD],                        // format for KEYBOARD
            $match[2][static::MATCH_STRING]                             // string to display in client
        );

    }

    private function _handle_code(&$match) {

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_CODE],                            // format for CODE
            $match[2][static::MATCH_STRING]                             // string to display in client
        );

    }

    private function _handle_link(&$match) {

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_LINK],                            // format for LINK
            $match[2][static::MATCH_STRING],                            // string for href attribute
            isset($match[3]) ? $match[3][static::MATCH_STRING] : null,  // string for title attribute
            $match[1][static::MATCH_STRING]                             // string to display in client
        );

    }

    // TODO: Implement references

    private function _handle_abbreviation(&$match) {

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_ABBREVIATION],                    // format for ABBREVIATION
            $match[2][static::MATCH_STRING],                            // string for title attribute
            $match[1][static::MATCH_STRING]                             // string to display in client
        );

    }

    private function _handle_sub(&$match) {

        // convert inline formatting of the resulting string
        $match[0][static::MATCH_RESULT] = $this->_convert_inline(preg_replace(
            "(^\\((.+?)\\)$)",                                          // match string
            "\\1",                                                      // remove paragraphs
            $match[2][static::MATCH_STRING]                             // string to be formatted
        ));

        // for each occurrence of the syntax, apply format
        for ($i = 0; $i < strlen($match[1][static::MATCH_STRING]); $i++) $match[0][static::MATCH_RESULT] = sprintf(

            $this->formats[static::ID_SUB],                             // format for SUB
            $match[0][static::MATCH_RESULT]                             // string inside previous SUB

        );

    }

    private function _handle_sup(&$match) {

        // convert inline formatting of the resulting string
        $match[0][static::MATCH_RESULT] = $this->_convert_inline(preg_replace(
            "(^\\((.+?)\\)$)",                                          // match string
            "\\1",                                                      // remove paragraphs
            $match[2][static::MATCH_STRING]                             // string to be formatted
        ));

        // for each occurrence of the syntax, apply format
        for ($i = 0; $i < strlen($match[1][static::MATCH_STRING]); $i++) $match[0][static::MATCH_RESULT] = sprintf(

            $this->formats[static::ID_SUP],                             // format for SUP
            $match[0][static::MATCH_RESULT]                             // string inside previous SUP

        );

    }

    private function _handle_strikethrough(&$match) {

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_STRIKETHROUGH],                   // format for STRIKEOUT
            $match[2][static::MATCH_STRING]                             // string to be formatted
        );

    }

    private function _handle_strong(&$match) {

        $text = $match[2][static::MATCH_STRING];                        // string to be formatted
        $sundown = [];

        // process the inline patterns we allow inside STRONG
        $this->_process_pattern(static::ID_EMPHASIS, $text, $sundown);

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_STRONG],                          // format for STRONG
            $this->_get_inline_result($text, $sundown)                  // get the result of the string
        );

    }

    private function _handle_emphasis(&$match) {

        $text = $match[2][static::MATCH_STRING];                        // string to be formatted
        $sundown = [];

        // process the inline patterns we allow inside EMPHASIS
        $this->_process_pattern(static::ID_STRONG, $text, $sundown);

        $match[0][static::MATCH_RESULT] = sprintf(
            $this->formats[static::ID_EMPHASIS],                        // format for EMPHASIS
            $this->_get_inline_result($text, $sundown)                  // get the result of the string
        );

    }

    private function _handle_linebreak(&$match) {

        // LINEBREAK only display the format
        $match[0][static::MATCH_RESULT] = $this->formats[static::ID_LINEBREAK];

    }

    private function _process_pattern($id, $text, &$sundown) {

        // grab all the matches from the text
        preg_match_all($this->patterns[$id], $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        // exit if no matches were found
        if (empty($matches)) return;

        // add MATCH_BOUNDARY as a value to the match
        foreach ($matches as &$match) $match[0][static::MATCH_BOUNDARY] = $match[0][static::MATCH_ORIGIN] + strlen($match[0][static::MATCH_STRING]);

        // filter all matches of the pattern
        // these are referred to as "current match"
        $matches = array_filter($matches, function (&$match) use (&$sundown) {

            // filter all previous matches that are found to be within the current match
            // also iterates to check if current match isn't inside any previous matches
            foreach ($sundown as &$matches) $matches = array_filter($matches, function ($_match) use (&$match) {

                if (($match[0][static::MATCH_ORIGIN] >= $_match[0][static::MATCH_ORIGIN] &&         // if (ORIGIN is after other ORIGIN
                        $match[0][static::MATCH_ORIGIN] <= $_match[0][static::MATCH_BOUNDARY]) ||   // and before other BOUNDARY)
                    ($match[0][static::MATCH_BOUNDARY] >= $_match[0][static::MATCH_ORIGIN] &&       // or if (BOUNDARY is after other ORIGIN
                        $match[0][static::MATCH_BOUNDARY] <= $_match[0][static::MATCH_BOUNDARY]))   // and before other BOUNDARY)

                    // empty current match
                    $match = null;

                if ($match[0][static::MATCH_ORIGIN] <= $_match[0][static::MATCH_ORIGIN] &&          // if (ORIGIN is before other ORIGIN
                    $match[0][static::MATCH_BOUNDARY] >= $_match[0][static::MATCH_BOUNDARY])        // and BOUNDARY after other BOUNDARY)

                    // destroy previous match, since it has a parent (I do not support prolicide, but this is an exception)
                    return false;

                return $_match;

            });

            // destroy match if empty
            if (empty($match)) return false;
            else return $match;

        });

        // store the matches inside the sundown array
        $sundown[$id] = $matches;

    }

    private function _convert_block($text) {

        $sundown = [];

        // process all block patterns
        if ($this->flags ^ static::FLAG_NO_SCRIPTS) $this->_process_pattern(static::ID_SCRIPT, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_QUOTES) $this->_process_pattern(static::ID_QUOTE, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_TABLES) $this->_process_pattern(static::ID_TABLE, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_FIGURES) $this->_process_pattern(static::ID_FIGURE, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_IMAGES) $this->_process_pattern(static::ID_IMAGE, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_FRAMES) $this->_process_pattern(static::ID_FRAME, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_DESCRIPTION_LIST) $this->_process_pattern(static::ID_DESCRIPTION_LIST, $text, $sundown);
        $this->_process_pattern(static::ID_ORDERED_LIST, $text, $sundown);
        $this->_process_pattern(static::ID_UNORDERED_LIST, $text, $sundown);
        $this->_process_pattern(static::ID_NUMBERED_HEADER, $text, $sundown);
        $this->_process_pattern(static::ID_UNDERLINED_HEADER, $text, $sundown);
        $this->_process_pattern(static::ID_HORIZONTAL_RULE, $text, $sundown);
        $this->_process_pattern(static::ID_PARAGRAPH, $text, $sundown);

        // return the result of the input string
        return $this->_get_block_result($sundown);

    }

    private function _convert_inline($text) {

        $sundown = [];

        // process all inline patterns
        if ($this->flags ^ static::FLAG_NO_KEYBOARD) $this->_process_pattern(static::ID_KEYBOARD, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_CODES) $this->_process_pattern(static::ID_CODE, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_LINKS) $this->_process_pattern(static::ID_LINK, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_ABBREVIATIONS) $this->_process_pattern(static::ID_ABBREVIATION, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_SUB) $this->_process_pattern(static::ID_SUB, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_SUP) $this->_process_pattern(static::ID_SUP, $text, $sundown);
        if ($this->flags ^ static::FLAG_NO_STRIKETHROUGH) $this->_process_pattern(static::ID_STRIKETHROUGH, $text, $sundown);
        $this->_process_pattern(static::ID_STRONG, $text, $sundown);
        $this->_process_pattern(static::ID_EMPHASIS, $text, $sundown);
        $this->_process_pattern(static::ID_LINEBREAK, $text, $sundown);

        // return the result of the input string
        return $this->_get_inline_result($text, $sundown);

    }

    private function _get_block_result(&$sundown) {

        // if the sundown contains paragraphs, make sure consecutive paragraphs are merged properly
        if (isset($sundown[static::ID_PARAGRAPH])) {

            foreach ($sundown[static::ID_PARAGRAPH] as &$match) {

                // if previous match is connected with current match
                if (isset($previous_match) && ($previous_match[0][static::MATCH_BOUNDARY] + 1) == $match[0][static::MATCH_ORIGIN]) {

                    // merge previous match and current match, then empty previous match
                    $match[0][static::MATCH_STRING] = $previous_match[0][static::MATCH_STRING] . "\n" . $match[0][static::MATCH_STRING];
                    $match[0][static::MATCH_ORIGIN] = $previous_match[0][static::MATCH_ORIGIN];
                    $previous_match = null;

                }

                // reference current match as previous match, for next iteration
                $previous_match = &$match;

            }

            unset($previous_match, $match);                                                         // clear reference
            $sundown[static::ID_PARAGRAPH] = array_filter($sundown[static::ID_PARAGRAPH]);          // destroy empty paragraphs

        }

        if (isset($sundown[static::ID_SCRIPT])) foreach ($sundown[static::ID_SCRIPT] as &$match) $this->_handle_script($match);
        if (isset($sundown[static::ID_QUOTE])) foreach ($sundown[static::ID_QUOTE] as &$match) $this->_handle_quote($match);
        if (isset($sundown[static::ID_TABLE])) foreach ($sundown[static::ID_TABLE] as &$match) $this->_handle_table($match);
        if (isset($sundown[static::ID_FIGURE])) foreach ($sundown[static::ID_FIGURE] as &$match) $this->_handle_figure($match);
        if (isset($sundown[static::ID_IMAGE])) foreach ($sundown[static::ID_IMAGE] as &$match) $this->_handle_image($match);
        if (isset($sundown[static::ID_FRAME])) foreach ($sundown[static::ID_FRAME] as &$match) $this->_handle_frame($match);
        if (isset($sundown[static::ID_ORDERED_LIST])) foreach ($sundown[static::ID_ORDERED_LIST] as &$match) $this->_handle_ordered_list($match);
        if (isset($sundown[static::ID_UNORDERED_LIST])) foreach ($sundown[static::ID_UNORDERED_LIST] as &$match) $this->_handle_unordered_list($match);
        if (isset($sundown[static::ID_DESCRIPTION_LIST])) foreach ($sundown[static::ID_DESCRIPTION_LIST] as &$match) $this->_handle_description_list($match);
        if (isset($sundown[static::ID_NUMBERED_HEADER])) foreach ($sundown[static::ID_NUMBERED_HEADER] as &$match) $this->_handle_numbered_header($match);
        if (isset($sundown[static::ID_UNDERLINED_HEADER])) foreach ($sundown[static::ID_UNDERLINED_HEADER] as &$match) $this->_handle_underlined_header($match);
        if (isset($sundown[static::ID_HORIZONTAL_RULE])) foreach ($sundown[static::ID_HORIZONTAL_RULE] as &$match) $this->_handle_horizontal_rule($match);
        if (isset($sundown[static::ID_PARAGRAPH])) foreach ($sundown[static::ID_PARAGRAPH] as &$match) $this->_handle_paragraph($match);

        // destroy empty matches
        $this->_destroy_empty($sundown);

        $text = null;                                                                               // make empty string
        foreach ($this->_sort_matches($sundown) as $match) $text .= $match[static::MATCH_RESULT];   // append each consecutive block

        return $text;

    }

    private function _get_inline_result($text, &$sundown) {

        // check if we're working with any of the patterns; then handle the matches
        if (isset($sundown[static::ID_KEYBOARD])) foreach ($sundown[static::ID_KEYBOARD] as &$match) $this->_handle_keyboard($match);
        if (isset($sundown[static::ID_CODE])) foreach ($sundown[static::ID_CODE] as &$match) $this->_handle_code($match);
        if (isset($sundown[static::ID_LINK])) foreach ($sundown[static::ID_LINK] as &$match) $this->_handle_link($match);
        if (isset($sundown[static::ID_ABBREVIATION])) foreach ($sundown[static::ID_ABBREVIATION] as &$match) $this->_handle_abbreviation($match);
        if (isset($sundown[static::ID_SUB])) foreach ($sundown[static::ID_SUB] as &$match) $this->_handle_sub($match);
        if (isset($sundown[static::ID_SUP])) foreach ($sundown[static::ID_SUP] as &$match) $this->_handle_sup($match);
        if (isset($sundown[static::ID_STRONG])) foreach ($sundown[static::ID_STRONG] as &$match) $this->_handle_strong($match);
        if (isset($sundown[static::ID_EMPHASIS])) foreach ($sundown[static::ID_EMPHASIS] as &$match) $this->_handle_emphasis($match);
        if (isset($sundown[static::ID_STRIKETHROUGH])) foreach ($sundown[static::ID_STRIKETHROUGH] as &$match) $this->_handle_strikethrough($match);
        if (isset($sundown[static::ID_LINEBREAK])) foreach ($sundown[static::ID_LINEBREAK] as &$match) $this->_handle_linebreak($match);
        if (isset($sundown[static::ID_LINEBREAK])) foreach ($sundown[static::ID_LINEBREAK] as &$match) $this->_handle_linebreak($match);

        // destroy empty matches
        $this->_destroy_empty($sundown);

        // _sort_matches() has to run reverse in order to correctly insert the replacements
        foreach ($this->_sort_matches($sundown, true) as $match) $text = substr_replace(
            $text,                                                          // subject string, the string we make changes to
            $match[static::MATCH_RESULT],                                   // replacement string to insert into subject string
            $match[static::MATCH_ORIGIN],                                   // the origin point, where we start our replacement
            $match[static::MATCH_BOUNDARY] - $match[static::MATCH_ORIGIN]   // the string length of our original string
        );

        return $text;

    }

    private function _destroy_empty(&$sundown) {

        // go trough every set of matches and filter out empty matches
        foreach ($sundown as &$matches) $matches = array_filter($matches, function (&$match) {

            if (!isset($match[0][static::MATCH_RESULT])) return false;      // destroy matches with no result

            return $match;

        });

    }

    private function _sort_matches(&$sundown, $reverse = false) {

        $sorted_matches = [];

        // collect all the relevant data into one array
        foreach ($sundown as &$matches) foreach ($matches as &$match) array_push($sorted_matches, $match[0]);

        // sort the array beginning with the first match, or last match if $reverse = true
        usort($sorted_matches, function ($lhs, $rhs) use ($reverse) {

            if ($reverse) return $lhs[static::MATCH_ORIGIN] < $rhs[static::MATCH_ORIGIN];   // sort ascending
            else return $lhs[static::MATCH_ORIGIN] > $rhs[static::MATCH_ORIGIN];            // sort descending

        });

        return $sorted_matches;

    }

}