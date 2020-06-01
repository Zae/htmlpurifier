<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\AttrDef\URI\Email;

use HTMLPurifier\AttrDef\URI\Email\SimpleCheck;

/**
 * Class SimpleCheckTest
 *
 * @package HTMLPurifier\Tests\Unit\AttrDef\URI\Email
 */
class SimpleCheckTest extends TestCase
{
    protected function setUp(): void
    {
        $this->def = new SimpleCheck();
    }
}
