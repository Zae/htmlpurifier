<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Unit\FSTools;

use FSTools;

/**
 * Class TestCase
 *
 * @package HTMLPurifier\Tests\Unit\FSTools
 */
class TestCase extends \HTMLPurifier\Tests\Unit\TestCase
{
    protected $dir;
    protected $oldDir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/htmlpurifier/' . uniqid((string)mt_rand(), true) . '/';
        mkdir($this->dir, 0777, true);

        $this->oldDir = getcwd();
        chdir($this->dir);
    }

    protected function tearDown(): void
    {
        chdir($this->oldDir);
        FSTools::singleton()->rmdirr($this->dir);

        parent::tearDown();
    }
}
