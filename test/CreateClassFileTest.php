<?php

declare(strict_types=1);


namespace AntidotTest\DevTools\Application\Service;

use Antidot\DevTools\Application\Service\CreateClassFile;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CreateClassFileTest extends TestCase
{
    private CreateClassFile $createClassFile;

    private string $classDir;

    private string $className = 'Test';

    private string $content = "<?php class Test{}\n";

    protected function setUp(): void
    {
        vfsStream::setup('rootDir');
        $this->classDir = vfsStream::url('rootDir');
        $this->createClassFile = new CreateClassFile();
    }

    public function testItShouldReturnClassFileDirectory(): void
    {
        $expected = $this->classDir . '/' . $this->className . '.php';

        $this->assertSame($expected, $this->createClassFile->__invoke($this->classDir, $this->className, $this->content));
    }

    public function testShouldThrowRuntimeExceptionOnDirectoryWasNotCreated()
    {
        $this->expectException(RuntimeException::class);

        $invalidDir = 'php://temp';
        $this->createClassFile->__invoke($invalidDir, $this->className, $this->content);
    }

    public function testShouldThrowRuntimeExceptionOnFileIsAlreadyExisted()
    {
        $this->expectException(RuntimeException::class);

        $existedDir = __DIR__ . '/fixtures';
        $existedClassName = 'ExistedClass';
        $this->createClassFile->__invoke($existedDir, $existedClassName, $this->content);
    }
}
