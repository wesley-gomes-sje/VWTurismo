<?php

namespace Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\SanitizeTrait;

class SanitizeTraitTest extends TestCase
{
    private object $subject;

    protected function setUp(): void
    {
        $this->subject = new class {
            use SanitizeTrait;

            public function sanitize(?string $s): string
            {
                return $this->sanitizeString($s);
            }
        };
    }

    public function testStripsHtmlTags(): void
    {
        $this->assertSame('hello', $this->subject->sanitize('<b>hello</b>'));
    }

    public function testStripsTagLeavingEmptyString(): void
    {
        // strip_tags remove a tag inteira — nada sobra para escapar
        $this->assertSame('', $this->subject->sanitize('<script>'));
    }

    public function testEscapesAmpersand(): void
    {
        $this->assertSame('a &amp; b', $this->subject->sanitize('a & b'));
    }

    public function testEscapesDoubleQuotes(): void
    {
        $this->assertSame('say &quot;hi&quot;', $this->subject->sanitize('say "hi"'));
    }

    public function testEscapesSingleQuotes(): void
    {
        $this->assertSame('it&#039;s', $this->subject->sanitize("it's"));
    }

    public function testHandlesNullInput(): void
    {
        $this->assertSame('', $this->subject->sanitize(null));
    }

    public function testHandlesEmptyString(): void
    {
        $this->assertSame('', $this->subject->sanitize(''));
    }

    public function testPlainTextUnchanged(): void
    {
        $this->assertSame('São Paulo', $this->subject->sanitize('São Paulo'));
    }
}
