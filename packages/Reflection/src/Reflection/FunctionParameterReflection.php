<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class FunctionParameterReflection implements ParameterReflectionInterface
{
    /**
     * @var ReflectionParameter
     */
    private $reflection;

    /**
     * @var MethodReflectionInterface|FunctionReflectionInterface
     */
    private $declaringFunction;

    public function __construct(
        ReflectionParameter $betterParameterReflection
    ) {
        $this->reflection = $betterParameterReflection;
    }

    public function getPrettyName(): string
    {
        return str_replace(
            '()',
            '($' . $this->reflection->getName() . ')',
            $this->declaringFunction->getPrettyName()
        );
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    /**
     * @return MethodReflectionInterface|FunctionReflectionInterface
     */
    public function getDeclaringFunction()
    {
        return $this->declaringFunction;
    }

    public function getDeclaringFunctionName(): string
    {
        return $this->declaringFunction->getName();
    }

    public function getTypeHint(): string
    {
        if ($this->isArray()) {
            return 'array';
        }

        if ($this->reflection->isCallable()) {
            return 'callable';
        }

        $className = $this->getClassName();
        if ($className) {
            return $className;
        }

        $annotation = $this->getAnnotation();
        if ($annotation) {
            return (string) $annotation->getType();
        }

        return '';
    }

    public function getDescription(): string
    {
        $annotations = $this->declaringFunction->getAnnotation(AnnotationList::PARAM);
        if (empty($annotations[$this->reflection->getPosition()])) {
            return '';
        }

        /** @var Param $paramAnnotation */
        $paramAnnotation = $annotations[$this->reflection->getPosition()];

        return $paramAnnotation->getDescription()
            ->render();
    }

    public function getDefaultValueDefinition(): ?string
    {
        if ($this->reflection->isDefaultValueAvailable()) {
            return $this->reflection->getDefaultValueAsString();
        }

        return null;
    }

    public function isArray(): bool
    {
        return $this->reflection->isArray();
    }

    public function getClass(): ?ClassReflectionInterface
    {
        $typeHint = $this->reflection->getTypeHint();
        if ($typeHint) {
            // @todo
        }

        return null;
    }

    public function getClassName(): ?string
    {
        $class = $this->getClass();
        if ($class) {
            return $class->getName();
        }

        return null;
    }

    public function isVariadic(): bool
    {
        return $this->reflection->isVariadic();
    }

    public function isCallable(): bool
    {
        return $this->reflection->isCallable();
    }

    /**
     * @param MethodReflectionInterface|FunctionReflectionInterface $declaringFunction
     */
    public function setDeclaringFunction($declaringFunction): void
    {
        $this->declaringFunction = $declaringFunction;
    }

    private function getAnnotation(): ?Param
    {
        $annotations = $this->declaringFunction->getAnnotation(AnnotationList::PARAM);
        if (empty($annotations[$this->reflection->getPosition()])) {
            return null;
        }

        return $annotations[$this->reflection->getPosition()];
    }
}