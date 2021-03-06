<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionInterface;
use Nette\Object;
use TokenReflection\IReflection;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionFunction;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionParameter;
use TokenReflection\IReflectionProperty;

abstract class AbstractReflection extends Object implements ReflectionInterface
{
    /**
     * @var string
     */
    protected $reflectionType;

    /**
     * @var IReflectionClass|IReflectionFunction|IReflectionMethod|IReflectionParameter|IReflectionProperty
     */
    protected $reflection;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var ParserStorageInterface
     */
    protected $parserStorage;

    /**
     * @var ReflectionFactoryInterface
     */
    protected $reflectionFactory;

    public function __construct(IReflection $reflection)
    {
        $this->reflectionType = get_class($this);
        $this->reflection = $reflection;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getPrettyName(): string
    {
        return $this->reflection->getPrettyName();
    }

    public function isInternal(): bool
    {
        return $this->reflection->isInternal();
    }

    public function isTokenized(): bool
    {
        return $this->reflection->isTokenized();
    }

    public function getFileName(): string
    {
        return $this->reflection->getFileName();
    }

    public function getStartLine(): int
    {
        $startLine = $this->reflection->getStartLine();
        $doc = $this->getDocComment();

        if ($doc) {
            $startLine -= substr_count($doc, "\n") + 1;
        }

        return $startLine;
    }

    public function getEndLine(): int
    {
        return $this->reflection->getEndLine();
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function setParserStorage(ParserStorageInterface $parserStorage): void
    {
        $this->parserStorage = $parserStorage;
    }

    public function setReflectionFactory(ReflectionFactoryInterface $reflectionFactory): void
    {
        $this->reflectionFactory = $reflectionFactory;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses(): array
    {
        return $this->parserStorage->getElementsByType(ElementsInterface::CLASSES);
    }
}
