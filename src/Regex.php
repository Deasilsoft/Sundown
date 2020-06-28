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

class Regex
{
    const PATTERNS = [
        ID::SCRIPT => "
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
        ID::QUOTE => "
        (                                                               # [0]
            ^                                                           # line begin
            >\\ .+?                                                     # match content (first line)
            (?:                                                         # repeatable start
                \\R                                                     # line new
                >\\ .+?                                                 # match content
            )*                                                          # repeatable end
            $                                                           # line end
        )mx",
        ID::TABLE => "
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
        ID::FIGURE => "
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
        ID::IMAGE => "
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
        ID::FRAME => "
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
        ID::DESCRIPTION_LIST => "
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
        ID::ORDERED_LIST => "
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
        ID::UNORDERED_LIST => "
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
        ID::NUMBERED_HEADER => "
        (                                                               # [0]
            ^                                                           # line begin
            (\\#{1,6})\\                                                # [1] match syntax
            (.+?)                                                       # [2] match content
            \\1?                                                        # optional syntax
            $                                                           # line end
        )mx",
        ID::UNDERLINED_HEADER => "
        (                                                               # [0]
            ^                                                           # line begin
            (.+?)                                                       # [1] match content
            \\R                                                         # line new
            (-|=)\\2{2,}                                                # [2] match syntax
            $                                                           # line end
        )mx",
        ID::HORIZONTAL_RULE => "
        (                                                               # [0]
            ^                                                           # line begin
            ([\\-=*~_])\\1{2,}                                          # [1] match syntax
            $                                                           # line end
        )mx",
        ID::PARAGRAPH => "
        (                                                               # [0]
            ^                                                           # line begin
            .+?                                                         # match content
            $                                                           # line end
        )mx",
        ID::KEYBOARD => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (``)                                                        # [1] match begin
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",
        ID::CODE => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (`)                                                         # [1] match begin
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",
        ID::LINK => "
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
        ID::ABBREVIATION => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            \\{                                                         # match syntax
                (.+?)                                                   # [1] match content
            \\}\\(                                                      # match syntax
                (.+?)                                                   # [2] match title
            \\)                                                         # match syntax
        )mx",
        ID::SUB => "
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
        ID::SUP => "
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
        ID::STRIKETHROUGH => "
        (
            (?<!\\\\)                                                   # match escape
            (~~)                                                        # [1] match start
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",
        ID::STRONG => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (__|\\*\\*)                                                 # [1] match start
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",
        ID::EMPHASIS => "
        (                                                               # [0]
            (?<!\\\\)                                                   # match escape
            (_|\\*)                                                     # [1] match start
            (.+?)                                                       # [2] match content
            (?<!\\\\)                                                   # match escape
            \\1                                                         # match end
        )mx",
        ID::LINEBREAK => "
        (                                                               # [0]
            (?<!^)                                                      # match line begin (NO BLANK LINES)
            (?<!\\\\)                                                   # match escape
            \\ {2,}                                                     # match syntax
            $                                                           # line end
        )mx",
    ];
}
