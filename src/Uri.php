<?php
namespace QuickRouter;

use \Psr\Http\Message\StreamInterface;

/**
 * Value object representing a URI.
 *
 * This interface is meant to represent URIs according to RFC 3986 and to
 * provide methods for most common operations. Additional functionality for
 * working with URIs can be provided on top of the interface or externally.
 * Its primary use is for HTTP requests, but may also be used in other
 * contexts.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * Typically the Host header will be also be present in the request message.
 * For server-side requests, the scheme will typically be discoverable in the
 * server parameters.
 *
 * @link http://tools.ietf.org/html/rfc3986 (the URI specification)
 */
class Uri implements UriInterface {

    protected $scheme = "";
    protected $user = "";
    protected $pass = "";
    protected $host = "";
    protected $port = "";
    protected $path = "";
    protected $query = "";
    protected $fragment = "";

    public function __construct($uri = "") {
        $parsed = parse_url($uri);
        if (is_array($parsed)) {
            foreach (["scheme", "host", "port", "user", "pass", "path", "query", "fragment"] as $field) {
                if (!empty($parsed[$field])) $this->$field = $parsed[$field];
            }
        }
    }

    public function getScheme() {
        return $this->scheme;
    }

    public function getAuthority() {
        $authority = $this->getHost();
        if ($this->getUserInfo()) {
            $authority = $this->getUserInfo() . "@" . $authority;
        }
        if ($this->getPort()) {
            $authority .= ":" . $this->getPort();
        }
        return $authority;
    }

    public function getUserInfo() {
        if (!$this->user) {
            return "";
        }
        return $this->pass ? $this->user . ":" . $this->pass : $this->user;
    }

    public function getHost() {
        return $this->host;
    }

    public function getPort() {
        if (empty($this->port)) return null;
        if (empty($this->port) && empty($this->scheme)) return null;
        $scheme = strtolower($this->getScheme());
        $port = (int)$port;
        $isStandard = ($scheme === "http" && $port === 80) || ($scheme === "https" && $port === 443);
        return $isStandard ? null : $port;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath() {
        //@todo ^^
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery() {
        //@todo percent encoding
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment() {
        //@todo percent encoding
        return ltrim($this->fragment, "#");
    }

    public function withScheme($scheme) {
        $scheme = strtolower($scheme);
        if (!in_array($scheme, $this->validSchemes())) {
            throw new \InvalidArgumentException("Invalid scheme. Scheme must be one of " . implode(",", $this->validSchemes()));
        }
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    public function withUserInfo($user, $password = null) {
        $clone = clone $this;
        $clone->user = empty($user) ? "" : $user;
        $clone->pass = empty($password) ? "" : $password;
        return $clone;
    }

    public function withHost($host) {
        $clone = clone $this;
        $clone->host = empty($host) ? "" : $host;
        return $clone;
    }

    public function withPort($port) {
        if ($port !== null) $port = (int)$port;
        if ($port !== null && ($port < 0 || $port > 65353)) {
            throw new \InvalidArgumentException("Invalid port - outside of established port ranges");
        }
        $clone = clone $this;
        $clone->port = $port === null ? "" : $port;
        return $clone;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path) {
        //@todo
        if (!is_string($path)) {
            throw new \InvalidArgumentException("Invalid path - must be string");
        }

        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function withQuery($query) {
        if (!is_string($query)) {
            throw new \InvalidArgumentException("Invalid query - must be string"); 
        }
        $clone = clone $this;
        $clone->query = $query;
        return $clone;
    }

    public function withFragment($fragment) {
        if (!is_string($query)) {
            throw new \InvalidArgumentException("Invalid fragment - must be string"); 
        }
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString() {

    }

    protected function validSchemes() {
        return ["", "http","https"];
    }
}
