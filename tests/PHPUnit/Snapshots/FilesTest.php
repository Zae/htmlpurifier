<?php

declare(strict_types=1);

namespace HTMLPurifier\Tests\Snapshots;

use HTMLPurifier\Tests\Traits\TestUtilities;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class FilesTest
 *
 * @package PHPUnit\Snapshots
 */
class FilesTest extends TestCase
{
    use MatchesSnapshots;
    use TestUtilities;

    /**
     * @test
     * @group        file
     * @dataProvider configProvider
     *
     * @param array $config
     */
    public function fileTest(array $config = []): void
    {
        $htmlPurifier = $this->createHtmlPurifier($config);

        $files = $this->getFiles();

        foreach($files as $file) {
            $this->assertMatchesTextSnapshot(
                $htmlPurifier->purify(file_get_contents($file))
            );
        }
    }

    /**
     * @return array
     */
    private function getFiles(): array
    {
        return glob(__DIR__ . '/../files/html/*.html');
    }
}
