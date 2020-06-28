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

class Flag
{
    const NO_SCRIPTS = 1 << 0x000;
    const NO_CODES = 1 << 0x001;
    const NO_QUOTES = 1 << 0x002;
    const NO_TABLES = 1 << 0x003;
    const NO_FIGURES = 1 << 0x004;
    const NO_IMAGES = 1 << 0x005;
    const NO_FRAMES = 1 << 0x006;
    const NO_DESCRIPTION_LIST = 1 << 0x007;
    const NO_KEYBOARD = 1 << 0x008;
    const NO_LINKS = 1 << 0x009;
    const NO_ABBREVIATIONS = 1 << 0x00A;
    const NO_SUB = 1 << 0x00B;
    const NO_SUP = 1 << 0x00C;
    const NO_STRIKETHROUGH = 1 << 0x00D;
}
