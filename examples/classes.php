<?php namespace Little\Examples;

class Foo {

    public function __construct($var = 42)
    {
        echo "Got $var in Foo\n";
    }

}

class Bar {

    public function __construct(Foo $bar, $var = 'duck')
    {
        $bar = get_class($bar);

        echo "Got $var and an instance of $bar in Bar\n";
    }

}

class Baz {

    public function __construct(Foo $foo, Bar $bar)
    {
        list($foo, $bar) = [get_class($foo), get_class($bar)];

        echo "Got instances of $foo and $bar in Baz\n";
    }

}

