<?php

namespace PHPUnit\Framework {
    if (!class_exists(TestCase::class, false)) {
        abstract class TestCase
        {
            protected function assertTrue(bool $condition): void
            {
                if ($condition !== true) {
                    throw new \RuntimeException('Failed asserting that true is true.');
                }
            }
        }
    }
}

namespace Tests\Unit {
    use PHPUnit\Framework\TestCase;

    class ExampleTest extends TestCase
    {
        /**
         * A basic test example.
         */
        public function test_that_true_is_true(): void
        {
            $this->assertTrue(true);
        }
    }
}
