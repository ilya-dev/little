[![Build Status](https://travis-ci.org/ilya-dev/little.svg?branch=master)](https://travis-ci.org/ilya-dev/little)

**Little** is a very small IoC container written in PHP just for fun. This project is heavily inspired by the Laravel IoC container.

**Little** supports automatic dependency resolution, singletons and binding existing instances into the container.

**Little** consists of ~200 lines of code (including comments)
and 5 methods available to you:

+ `void bind(string $abstract, string|Closure $concrete)`
+ `mixed make(string $abstract)`
+ `void singleton(string $abstract, string|Closure $concrete)`
+ `void instance(string $abstract, mixed $instance)`
+ `boolean bound(string $abstract)`

Believe it or not, that's it!

# License

The MIT license, check out the `LICENSE` file. 

P.S. follow [the author](https://twitter.com/ilya_s_dev) on Twitter
