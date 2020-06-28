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

class HTML
{
    const FORMATS = [
        ID::SCRIPT => "<pre><code class='language-%1\$s' data-language='%1\$s'>\n%2\$s</code></pre>\n",
        ID::QUOTE => "<blockquote class='blockquote'>\n%s</blockquote>\n",
        ID::TABLE => [
            "table" => "<table class='table'>\n%s</table>\n",
            "thead" => "<thead>\n%s</thead>\n",
            "tbody" => "<tbody>\n%s</tbody>\n",
            "header" => [
                "row" => "<tr>\n%s</tr>\n",
                "cell" => [
                    "left" => "<th class='text-left' colspan='%1\$s' scope='%3\$s'>\n%2\$s\n</th>\n",
                    "center" => "<th class='text-center' colspan='%1\$s' scope='%3\$s'>\n%2\$s\n</th>\n",
                    "right" => "<th class='text-right' colspan='%1\$s' scope='%3\$s'>\n%2\$s\n</th>\n",
                ],
            ],
            "body" => [
                "row" => "<tr>\n%s</tr>\n",
                "cell" => [
                    "left" => "<td class='text-left' colspan='%1\$s'>\n%2\$s\n</td>\n",
                    "center" => "<td class='text-center' colspan='%1\$s'>\n%2\$s\n</td>\n",
                    "right" => "<td class='text-right' colspan='%1\$s'>\n%2\$s\n</td>\n",
                ],
            ],
        ],
        ID::FIGURE => [
            "default" => "<figure><img src='%s' class='float-none' title='%s' alt='%s'><figcaption>%s</figcaption></figure>\n",
            "left" => "<figure><img src='%s' class='float-left' title='%s' alt='%s'><figcaption>%s</figcaption></figure>\n",
            "right" => "<figure><img src='%s' class='float-right' title='%s' alt='%s'><figcaption>%s</figcaption></figure>\n",
        ],
        ID::IMAGE => [
            "default" => "<img src='%s' class='float-none' title='%s' alt='%s'>\n",
            "left" => "<img src='%s' class='float-left' title='%s' alt='%s'>\n",
            "right" => "<img src='%s' class='float-right' title='%s' alt='%s'>\n",
        ],
        ID::FRAME => "<iframe src='%s' style='border: 0; width: 854px; height: 480px;' allowfullscreen></iframe>\n",
        ID::ORDERED_LIST => [
            "list" => "<ol>\n%s</ol>\n",
            "item" => "<li>\n%s</li>\n",
        ],
        ID::UNORDERED_LIST => [
            "list" => "<ul>\n%s</ul>\n",
            "item" => "<li>\n%s</li>\n",
        ],
        ID::DESCRIPTION_LIST => [
            "list" => "<dl>\n%s</dl>\n",
            "title" => "<dt>\n%s\n</dt>\n",
            "item" => "<dd>\n%s\n</dd>\n",
        ],
        ID::NUMBERED_HEADER => "<h%1\$d>%2\$s</h%1\$d>\n",
        ID::UNDERLINED_HEADER => [
            "h1" => "<h1 class='display-2'>%s</h1>\n",
            "h2" => "<h2 class='display-4'>%s</h2>\n",
        ],
        ID::HORIZONTAL_RULE => "<hr>\n",
        ID::PARAGRAPH => "<p>\n%s\n</p>\n",
        ID::KEYBOARD => "<kbd>%s</kbd>",
        ID::CODE => "<code>%s</code>",
        ID::LINK => "<a href='%s' title='%s'>%s</a>",
        ID::ABBREVIATION => "<abbr title='%s'>%s</abbr>",
        ID::SUB => "<sub>%s</sub>",
        ID::SUP => "<sup>%s</sup>",
        ID::STRIKETHROUGH => "<s>%s</s>",
        ID::STRONG => "<strong>%s</strong>",
        ID::EMPHASIS => "<em>%s</em>",
        ID::LINEBREAK => "<br>",
    ];
}
