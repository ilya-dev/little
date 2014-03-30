<?php namespace Little\Examples;

# we do not leverage the Composer autoloader for the examples folder
require __DIR__.'/classes.php';
require __DIR__.'/../src/Little.php';

$little = new \Little;

###
# this code shows automatic dependency resolution,
# the ability to bind existing instances, bindings

echo "\n\nEXAMPLE #1\n\n";

$little->bind('Little\Examples\Foo', function($app)
{
    return new Foo(101);
});

$little->instance('Little\Examples\Bar', new Bar(new Foo, 'dog'));

$little->make('Little\Examples\Baz');

###
# Singletons in action

echo "\n\nEXAMPLE #2\n\n";

$little->singleton('dummy', 'stdClass');

$instance = $little->make('dummy');

$condition = ($instance === $little->make('dummy')) ? 'are' : 'are not';

echo "These two $condition equal\n";

###
# one last example - determine whether a given abstract has been bound

echo "\n\nEXAMPLE #3\n\n";

$random = 'apples'.rand();

$inform = function($binding) use($little)
{
    $condition = $little->bound($binding) ? 'has been' : 'has not been';

    echo "$binding $condition bound into the container\n";
};

$inform($random);

$little->bind($random, 'unicorns');

$inform($random);

echo "\n\n";
