# KTest

Ultra lightweight PHP testing framework for Codewars and kata solving.

## Installation

```bash
composer require sasyk/codewars-testkit
```

---

## Usage

### test.php

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/test-test.php';

(new MyTest)();

function solve()
{
    return 'hello';
}
```

### test-test.php

```php
<?php

use KTest\TestCase;

class MyTest extends TestCase
{
    public function testExample(): void
    {
        $this->assertSame(
            'hello',
            solve()
        );
    }
}
```

---

## Features

- Colored terminal output
- Assertions
- Lightweight
- No configuration
- Perfect for Codewars / kata

## License

MIT
