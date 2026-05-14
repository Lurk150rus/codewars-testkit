<?php
namespace KTest;

use Exception;
use ReflectionMethod;
use Throwable;
class TestCase
{
    private const COLOR_RED    = "\033[31m";
    private const COLOR_GREEN  = "\033[32m";
    private const COLOR_YELLOW = "\033[33m";
    private const COLOR_BLUE   = "\033[34m";
    private const COLOR_CYAN   = "\033[36m";
    private const COLOR_GRAY   = "\033[90m";
    private const COLOR_RESET  = "\033[0m";

    private int $passed = 0;
    private int $failed = 0;

    public function __invoke()
    {
        $className = static::class;

        echo PHP_EOL;
        echo self::COLOR_CYAN . "═══════════════════════════════════════" . self::COLOR_RESET . PHP_EOL;
        echo self::COLOR_CYAN . " Running tests: {$className}" . self::COLOR_RESET . PHP_EOL;
        echo self::COLOR_CYAN . "═══════════════════════════════════════" . self::COLOR_RESET . PHP_EOL;
        echo PHP_EOL;

        $start = microtime(true);

        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'test') !== 0) {
                continue;
            }

            $this->runTest($method);
        }

        $time = round((microtime(true) - $start) * 1000, 2);

        echo PHP_EOL;
        echo self::COLOR_CYAN . "═══════════════════════════════════════" . self::COLOR_RESET . PHP_EOL;

        if ($this->failed > 0) {
            echo self::COLOR_RED . " FAILED" . self::COLOR_RESET;
        } else {
            echo self::COLOR_GREEN . " SUCCESS" . self::COLOR_RESET;
        }

        echo " | ";
        echo self::COLOR_GREEN . "Passed: {$this->passed}" . self::COLOR_RESET;
        echo " | ";
        echo self::COLOR_RED . "Failed: {$this->failed}" . self::COLOR_RESET;
        echo " | ";
        echo self::COLOR_YELLOW . "Time: {$time} ms" . self::COLOR_RESET;

        echo PHP_EOL;
        echo self::COLOR_CYAN . "═══════════════════════════════════════" . self::COLOR_RESET . PHP_EOL;
        echo PHP_EOL;
    }

    private function runTest(string $method): void
    {
        try {
            $ref = new ReflectionMethod($this, $method);
            $docComment = $ref->getDocComment();

            $start = microtime(true);

            if (
                $docComment &&
                preg_match('/@dataProvider\s+([^\s]+)/', $docComment, $matches)
            ) {
                $dataProvider = $matches[1];

                $i = 1;

                foreach ($this->$dataProvider() as $args) {
                    $this->$method(...$args);

                    echo self::COLOR_GREEN . " ✔ " . self::COLOR_RESET;
                    echo "{$method}";
                    echo self::COLOR_GRAY . " [dataset #{$i}]" . self::COLOR_RESET;
                    echo PHP_EOL;

                    $this->passed++;
                    $i++;
                }
            } else {
                $this->$method();

                $time = round((microtime(true) - $start) * 1000, 2);

                echo self::COLOR_GREEN . " ✔ " . self::COLOR_RESET;
                echo "{$method}";
                echo self::COLOR_GRAY . " ({$time} ms)" . self::COLOR_RESET;
                echo PHP_EOL;

                $this->passed++;
            }
        } catch (Throwable $e) {
            $this->failed++;

            echo self::COLOR_RED . " ✘ {$method}" . self::COLOR_RESET . PHP_EOL;

            echo "   ";
            echo self::COLOR_YELLOW . $e->getMessage() . self::COLOR_RESET;
            echo PHP_EOL;

            echo "   ";
            echo self::COLOR_GRAY;
            echo $e->getFile() . ':' . $e->getLine();
            echo self::COLOR_RESET;
            echo PHP_EOL . PHP_EOL;
        }
    }

    public static function assertTrue($condition, string $message = 'Assertion failed'): void
    {
        if (!$condition) {
            throw new Exception($message);
        }
    }

    public static function assertIsString($value): void
    {
        if (!is_string($value)) {
            throw new Exception(
                'Failed asserting that value is string. Got: ' . gettype($value)
            );
        }
    }

    public static function assertIsNumeric($value): void
    {
        if (!is_numeric($value)) {
            throw new Exception(
                'Failed asserting that value is numeric. Got: ' . gettype($value)
            );
        }
    }

    public static function assertSame($expected, $actual): void
    {
        if ($expected !== $actual) {
            throw new Exception(
                "Failed asserting that two values are identical.\n" .
                "Expected: " . var_export($expected, true) . "\n" .
                "Actual: " . var_export($actual, true)
            );
        }
    }

    public static function assertEquals($expected, $actual): void
    {
        self::assertSame($expected, $actual);
    }

}
