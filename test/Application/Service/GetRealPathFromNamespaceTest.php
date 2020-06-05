<?php

declare(strict_types=1);


namespace AntidotTest\DevTools\Application\Service;

use Antidot\DevTools\Application\Service\GetClassNameFromFQCN;
use Antidot\DevTools\Application\Service\GetNamespaceFromFQCN;
use Antidot\DevTools\Application\Service\GetRealPathFromNamespace;
use PHPUnit\Framework\TestCase;

class GetRealPathFromNamespaceTest extends TestCase
{
    private const NOT_CONFIGURED_NAMESPACE = 'Some\Invalid\Namespace';
    private const EXCEEDED_DEPTH_NAMESPACE =
        'AntidotTest\Invalid\Namespace\Some\Invalid\Namespace\Invalid\Namespace\Some\Invalid\Namespace';
    private const MAXIMUM_DEPTH_NAMESPACE =
        'AntidotTest\DevTools\Application\Service\Valid\Namespace\Some\Valid\Namespace\As\MaximumDepth';
    private const MAXIMUM_DEPTH_PATH =
        '%s/Valid/Namespace/Some/Valid/Namespace/As/MaximumDepth';

    private GetRealPathFromNamespace $getRealPathFromNamespace;

    public function setUp(): void
    {
        $this->getRealPathFromNamespace = new GetRealPathFromNamespace(
            new GetClassNameFromFQCN(self::class),
            new GetNamespaceFromFQCN(self::class)
        );
    }

    public function testItShouldReturnAPathForGivenNamespace(): void
    {
        $this->assertEquals(
            realpath(__DIR__),
            realpath(dirname($this->getRealPathFromNamespace->__invoke(self::class)))
        );
    }

    public function testItShouldReturnAPathForGivenNamespaceWithMaximumConfigurableDepth(): void
    {
        $this->assertEquals(
            realpath(sprintf(self::MAXIMUM_DEPTH_PATH, __DIR__)),
            realpath(dirname($this->getRealPathFromNamespace->__invoke(self::MAXIMUM_DEPTH_NAMESPACE)))
        );
    }

    public function testItShouldThrowInvalidArgumentExceptionGivenNotConfiguredNamespace(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            GetRealPathFromNamespace::MAXIMUM_DEPTH_MESSAGE,
            self::NOT_CONFIGURED_NAMESPACE
        ));
        $this->getRealPathFromNamespace->__invoke(self::NOT_CONFIGURED_NAMESPACE);
    }

    public function testItShouldThrowInvalidArgumentExceptionGivenNamespaceExceededMaximumDepthLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            GetRealPathFromNamespace::MAXIMUM_DEPTH_MESSAGE,
            self::EXCEEDED_DEPTH_NAMESPACE
        ));
        $this->getRealPathFromNamespace->__invoke(self::EXCEEDED_DEPTH_NAMESPACE);
    }
}
