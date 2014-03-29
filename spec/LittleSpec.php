<?php namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LittleSpec extends ObjectBehavior {

    function it_is_initializable()
    {
        $this->shouldHaveType('Little');
    }

    function it_allows_you_to_bind_a_type_into_the_container()
    {
        $this->bind('foo', function()
        {
            return 'bar';
        });

        $this->make('foo')->shouldReturn('bar');
    }

    function it_passes_itself_to_the_given_closure_as_a_parameter()
    {
        $this->bind('foo', function($container)
        {
            return $container instanceof \Little;
        });

        $this->make('foo')->shouldReturn(true);
    }

    function it_allows_you_to_bind_a_shared_type_into_the_container()
    {
        $this->singleton('foo', function()
        {
            return new DummyClass;
        });

        $this->make('foo')->shouldBeEqualTo($this->make('foo'));
    }

    function it_allows_you_to_bind_an_existing_instance_into_the_container()
    {
        $dummy = new DummyClass;

        $this->instance('foo', $dummy);

        $this->make('foo')->shouldBeEqualTo($dummy);
    }

    function it_resolves_key_conflicts()
    {
        $this->instance('foo', new DummyClass);

        $this->bind('foo', function()
        {
            return 'bar';
        });

        $this->make('foo')->shouldReturn('bar');
    }

    function it_allows_you_to_determine_whether_the_given_type_has_been_bound()
    {
        $this->bound('foo')->shouldReturn(false);
        $this->bind('foo', 'bar');
        $this->bound('foo')->shouldReturn(true);

        $this->bound('bar')->shouldReturn(false);
        $this->instance('bar', new DummyClass);
        $this->bound('bar')->shouldReturn(true);
    }

    function it_allows_you_to_pass_class_name_as_a_concrete_type()
    {
        $this->bind('foo', 'spec\DummyClass');

        $this->make('foo')->shouldBeAnInstanceOf('spec\DummyClass');
    }

    function it_resolves_the_class_dependencies()
    {
        $this->make($class = 'spec\ComplicatedClass')
             ->shouldBeAnInstanceOf($class);
    }

    function it_throws_an_exception_if_a_given_class_is_not_instantiable()
    {
        $class = 'class'.rand();

        $this->shouldThrow('LittleException')->duringMake($class);
    }

    function it_throws_an_exception_if_a_given_class_has_unresolvable_dependencies()
    {
        $this->shouldThrow('LittleException')->duringMake('UnresolvableClass');
    }

}

class DummyClass {}

class ComplicatedClass {

    public function __construct(DummyClass $dummy, $foo = null)
    {

    }

}

class UnresolvableClass {

    public function __construct($foo, $bar, $baz)
    {

    }

}

