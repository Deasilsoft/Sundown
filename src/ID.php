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

class ID
{
    /*
     * BLOCK IDs
     */
    const SCRIPT = 0x000;
    const QUOTE = 0x001;
    const TABLE = 0x002;
    const FIGURE = 0x003;
    const IMAGE = 0x004;
    const FRAME = 0x005;
    const DESCRIPTION_LIST = 0x006;
    const ORDERED_LIST = 0x007;
    const UNORDERED_LIST = 0x008;
    const NUMBERED_HEADER = 0x009;
    const UNDERLINED_HEADER = 0x00A;
    const HORIZONTAL_RULE = 0x00B;
    const PARAGRAPH = 0x00C;
    /*
     * INLINE IDs
     */
    const CODE = 0xF00;
    const KEYBOARD = 0xF01;
    const LINK = 0xF02;
    const ABBREVIATION = 0xF03;
    const SUB = 0xF04;
    const SUP = 0xF05;
    const STRIKETHROUGH = 0xF06;
    const STRONG = 0xF07;
    const EMPHASIS = 0xF08;
    const LINEBREAK = 0xF09;
}
