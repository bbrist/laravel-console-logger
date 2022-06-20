# laravel-console-logger

The `laravel-console-logger` package is a helper package designed to allow for more in-depth console logging

By default, adding this package to your project will automatically (via the ServiceProvider) start diverting 
logs (i.e. `Log::info()` statements) to console output. The log level that is output can be changed using
the verbosity flags for the Artisan console (i.e. `-v`, `-vv`, `-vvv`, `-q`).

With the default configuration, `-q` will output nothing, no verbosity args will output `error` and higher, `-v`
will output `warning` and higher, `-vv` will output `info` and higher, and `-vvv` will output all logs to console.

The `config/logging.php` configuration can be used to alter the default configuration. By default, `notice`, 
`error`, `critical`, `alert`, and `emergency` levels are logged to the `laravel.log` file in addition to the console
(even if `-q` is specified). To add/remove levels to include in the `laravel.log` file, just set/modify the `console.logMethods`
value in the `config/logging.php` file.

To log all logs to file, regardless of whether they are logged to console, you can set/modify the `console.logToFile` value
to `true` in the `config/logging.php` file.

### Force Log to File

The `Log::logToFile()` method will force any following logs to log to `laravel.log`. You can explicitly set
this status to `true` or `false` by providing the value as a parameter (i.e. `Log::logToFile(false)`).

This can be useful when you want to ensure that a chunk of code is logged to `laravel.log` regardless of where/how
it's executed (dependent on the value of `env('LOG_LEVEL')`).

For example:

```php
public function mymethod() {

    Log::logToFile(true);   // enable force log-to-file
    
    ... 
    
    Log::info("some message");  // This will always be logged to file (dependent on the LOG_LEVEL ENV value)
    
    ... 
    
    Log::logToFile(false);  // disable force log-to-file

}
```