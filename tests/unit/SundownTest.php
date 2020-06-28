<?php

class SundownTest extends Codeception\Test\Unit
{

    /** @var UnitTester */
    protected $tester;
    /** @var Deasilsoft\Sundown\Sundown */
    protected $sundown;

    protected function _before ()
    {
        $this->sundown = new Deasilsoft\Sundown\Sundown();
    }

    public function test_script ()
    {
        $output = $this->sundown->Convert("``` Javascript
    Test Script
```");

        $this->tester->comment("HELLO");
        $this->tester->assertContains("<pre><code class='language-javascript' data-language='javascript'>", $output);
        $this->tester->assertContains("</code></pre>", $output);
    }
}
