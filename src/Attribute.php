<?php

namespace FT\Reflection;

use Attribute as GlobalAttribute;
use FT\Reflection\Attributes\Inheritable;
use FT\Reflection\Exceptions\ReflectionException;
use ReflectionAttribute;
use ReflectionClass;

final class Attribute
{
    public readonly string $name;
    public readonly string $shortname;
    public readonly int $target;
    public readonly bool $is_repeated;
    private readonly object $arguments;
    public readonly bool $is_inheritable;

    public function __construct(private readonly ReflectionAttribute $attr)
    {
        $this->name = $attr->getName();
        $this->target = $attr->getTarget();
        $this->is_repeated = $attr->isRepeated();

        $rflc = new ReflectionClass($attr->getName());
        $this->shortname = $rflc->getShortName();

        $is_inheritable = !empty($rflc->getAttributes(Inheritable::class));

        if (!$is_inheritable) {
            $parent = $rflc;
            while (($parent = $parent->getParentClass()) !== false) {
                if (empty($parent->getAttributes(GlobalAttribute::class))) break;

                if ($parent->getAttributes(Inheritable::class)) {
                    $is_inheritable = true;
                    break;
                }
            }
        }

        $this->is_inheritable = $is_inheritable;
        $constr = $rflc->getConstructor();
        $params = [];
        if ($constr !== null)
            $params = array_map(fn ($i) => new Parameter($i), $constr->getParameters());

        $fargs = [];
        foreach ($attr->getArguments() as $key => $value) {
            if (is_int($key)) {
                if (count($params) > 1)
                    throw new ReflectionException("Positional arguments are not permitted for attributes with more than 1 parameter. Attribute members must be qualified by their name @" . $this->shortname);

                $fargs[$params[0]->name] = $value;
                continue;
            }

            $pindex = array_search($key, array_map(fn ($i) => $i->name, $params));
            if ($pindex === false)
                throw new ReflectionException("$key does not exist on @" . $this->shortname);

            $fargs[$key] = $value;
        }

        $out_params = array_diff(array_map(fn ($i) => $i->name, $params), array_keys($fargs));
        foreach ($out_params as $name) {
            $pindex = array_search($name, array_map(fn ($i) => $i->name, $params));
            if ($params[$pindex]->delegate->isDefaultValueAvailable())
                $fargs[$name] = $params[$pindex]->delegate->getDefaultValue();
        }

        $this->arguments = (object) $fargs;
    }

    public function getArguments(): object
    {
        return $this->arguments;
    }

    public function getArgument($name): mixed
    {
        if (property_exists($this->arguments, $name))
            return $this->arguments->{$name};

        return null;
    }

    public function newInstance()
    {
        $rflc = new ReflectionClass($this->name);
        $constr = $rflc->getConstructor();
        $args = [];
        if ($constr !== null) {
            $params = array_map(fn ($i) => new Parameter($i), $constr->getParameters());

            foreach ($params as $p)
                $args[] = $this->getArgument($p->name);

            return $rflc->newInstance(...$args);
        }

        return $this->attr->newInstance();
    }
}
