<?php

class SundownTest extends Codeception\Test\Unit {

    /** @var UnitTester */
    protected $tester;
    /** @var Deasilsoft\Sundown */
    protected $sundown;

    protected function _before() {

        require_once("src/Sundown.class.php");
        $this->sundown = new Deasilsoft\Sundown();

    }

    protected function _after() {
    }

    public function test_script() {

        $output = $this->sundown->convert("``` Javascript
    Test Script
```");

        $this->tester->assertContains("<pre><code class='language-javascript' data-language='javascript'>", $output);
        $this->tester->assertContains("</code></pre>", $output);

    }
}
