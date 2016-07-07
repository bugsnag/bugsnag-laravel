Upgrading
=========


## 1.x to 2.x

*Our library has gone through some major improvements. The primary change to watch out for is we're no longer overriding your exception handler.*

Since we're no longer overriding your exception handler, you'll need to restore your original handler, and then see our docs for how to bind our new logger to the container.

If you'd like access to all our new configuration, you'll need to re-publish our config file.
