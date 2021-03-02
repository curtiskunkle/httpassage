<h1>Rewriting Requests</h1>

Generally, this library will be used to route requests from a webserver to appropriate callbacks.  For this to work, you will need to configure your webserver to rewrite requests to the file that contains your router.  Most modern webservers contain functionality for rewriting requests.  Please refer to the documentation of your preferred webserver for information on how to rewrite requests.

Also, it is important to note that if the file that contains your router is in a subdirectory instead of the root of your server, you will either need to prepend the subdirectory when mapping routes, or set the basepath of your router to the directory.  See [route mapping](../routemapping/README.md) for more information.