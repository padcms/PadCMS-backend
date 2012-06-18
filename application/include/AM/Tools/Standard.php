<?php
/**
 * @file
 * AM_Tools_Standard class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Class contains wrappers for the basic php functions
 * @ingroup AM_Tools
 */
class AM_Tools_Standard
{
    /** @var AM_Tools_Standard */
    protected static $_oInstance = null; /**< @type AM_Tools_Standard */

    public function __construct()
    {
        self::$_oInstance = $this;
    }

    /**
     * @return AM_Tools_Standard
     */
    public static function getInstance()
    {
        if (is_null(self::$_oInstance)) {
            self::$_oInstance = new self();
        }

        return self::$_oInstance;
    }

    /**
     * Tells whether the filename is a directory
     * @link http://php.net/manual/en/function.is-dir.php
     * @param string $filename
     * @return bool true if the filename exists and is a directory, false otherwise.
     */
    public function is_dir($filename)
    {
        return is_dir($filename);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Tells whether the filename is a regular file
     * @link http://php.net/manual/en/function.is-file.php
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return bool true if the filename exists and is a regular file, false
     * otherwise.
     */
    public function is_file($filename)
    {
        return @is_file($filename);
    }

    /**
     * Makes directory
     * @link http://php.net/manual/en/function.mkdir.php
     * @param string $pathname
     * @param int $mode [optional]
     * @param bool $recursive [optional]
     * @return bool Returns true on success or false on failure.
     */
    public function mkdir($pathname, $mode = null, $recursive = null)
    {
        $oldUmask  = umask(0);
        $result = @mkdir($pathname, $mode, $recursive);
        umask($oldUmask);

        return $result;
    }

    /**
     * @link http://php.net/manual/en/function.copy.php
     * @param string $source
     * @param string $dest
     * @return bool Returns true on success or false on failure.
     */
    public function copy($source, $dest)
    {
        return @copy($source, $dest);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Renames a file or directory
     * @link http://php.net/manual/en/function.rename.php
     * @param string $oldname <p>
     * </p>
     * <p>
     * The old name. The wrapper used in oldname
     * must match the wrapper used in
     * newname.
     * </p>
     * @param string $newname <p>
     * The new name.
     * </p>
     * @param resource $context [optional] &note.context-support;
     * @return bool Returns true on success or false on failure.
     */
    public function rename($oldname, $newname)
    {
        return rename($oldname, $newname);
    }

    /**
     * Checks whether a file or directory exists
     * @link http://php.net/manual/en/function.file-exists.php
     * @param string $filename Path to the file or directory
     * @return bool true if the file or directory specified by filename exists; false otherwise.
     */
    public function file_exists($filename)
    {
        return @file_exists($filename);
    }

    /**
     * Deletes a file
     * @link http://php.net/manual/en/function.unlink.php
     * @param string $filename Path to the file.
     * @return bool Returns true on success or false on failure.
     */
    public function unlink($filename)
    {
        return @unlink($filename);
    }

    /**
     * Removes directory
     * @link http://php.net/manual/en/function.rmdir.php
     * @param string $dirname Path to the directory.
     * @return bool Returns true on success or false on failure.
     */
    public function rmdir($dirname)
    {
        return @rmdir($dirname);
    }

    /**
     * Clear dirrecroty recursive
     *
     * @param string $path
     * @return void
     */
    public function clearDir($dirname)
    {
        try {
            $dirIterator = new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS);
            $iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($iterator as $path) {
                if ($path->isDir()) {
                    $this->rmdir($path->__toString());
                } else {
                    $this->unlink($path->__toString());
                }
            }
        } catch (Exception $oException) {
            return false;
        }
    }

    /**
     * Returns TRUE if the $filename is readable, or FALSE otherwise.
     * This function uses the PHP include_path, where PHP's is_readable()
     * does not.
     *
     * Note : this method comes from Zend_Loader (see #ZF-2891 for details)
     *
     * @param string   $filename
     * @return boolean
     */
    public static function isReadable($filename)
    {
        if (!$fh = @fopen($filename, 'r', true)) {
            return false;
        }
        @fclose($fh);
        return true;
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Tells whether a file exists and is readable
     * @link http://php.net/manual/en/function.is-readable.php
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @return bool true if the file or directory specified by
     * filename exists and is readable, false otherwise.
     */
    public function is_readable($filename)
    {
        return @is_readable($filename);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Finds whether a variable is a resource
     * @link http://php.net/manual/en/function.is-resource.php
     * @param mixed $var <p>
     * The variable being evaluated.
     * </p>
     * @return bool true if var is a resource,
     * false otherwise.
     */
    public function is_resource($var)
    {
        return @is_resource($var);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Closes an open file pointer
     * @link http://php.net/manual/en/function.fclose.php
     * @param resource $handle <p>
     * The file pointer must be valid, and must point to a file successfully
     * opened by fopen or fsockopen.
     * </p>
     * @return bool Returns true on success or false on failure.
     */
    public function fclose($handle)
    {
        return @fclose($handle);
    }

    /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Create a streams context
     * @link http://php.net/manual/en/function.stream-context-create.php
     * @param array $options [optional] <p>
     * Must be an associative array of associative arrays in the format
     * $arr['wrapper']['option'] = $value.
     * </p>
     * <p>
     * Default to an empty array.
     * </p>
     * @param array $params [optional] <p>
     * Must be an associative array in the format
     * $arr['parameter'] = $value.
     * Refer to context parameters for
     * a listing of standard stream parameters.
     * </p>
     * @return resource A stream context resource.
     */
    public function stream_context_create($options = null, $params = null)
    {
        return stream_context_create($options, $params);
    }

    /**
     * (PHP 5)<br/>
     * Open Internet or Unix domain socket connection
     * @link http://php.net/manual/en/function.stream-socket-client.php
     * @param string $remote_socket <p>
     * Address to the socket to connect to.
     * </p>
     * @param int $errno [optional] <p>
     * Will be set to the system level error number if connection fails.
     * </p>
     * @param string $errstr [optional] <p>
     * Will be set to the system level error message if the connection fails.
     * </p>
     * @param float $timeout [optional] <p>
     * Number of seconds until the connect() system call
     * should timeout.
     * This parameter only applies when not making asynchronous
     * connection attempts.
     * <p>
     * To set a timeout for reading/writing data over the socket, use the
     * stream_set_timeout, as the
     * timeout only applies while making connecting
     * the socket.
     * </p>
     * </p>
     * @param int $flags [optional] <p>
     * Bitmask field which may be set to any combination of connection flags.
     * Currently the select of connection flags is limited to
     * STREAM_CLIENT_CONNECT (default),
     * STREAM_CLIENT_ASYNC_CONNECT and
     * STREAM_CLIENT_PERSISTENT.
     * </p>
     * @param resource $context [optional] <p>
     * A valid context resource created with stream_context_create.
     * </p>
     * @return resource On success a stream resource is returned which may
     * be used together with the other file functions (such as
     * fgets, fgetss,
     * fwrite, fclose, and
     * feof), false on failure.
     */
    public function stream_socket_client($remote_socket, &$errno = null, &$errstr = null, $timeout = null, $flags = null, $context = null)
    {
        return stream_socket_client($remote_socket, $errno, $errstr, $timeout, $flags, $context);
    }

    /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Set blocking/non-blocking mode on a stream
     * @link http://php.net/manual/en/function.stream-set-blocking.php
     * @param resource $stream <p>
     * The stream.
     * </p>
     * @param int $mode <p>
     * If mode is 0, the given stream
     * will be switched to non-blocking mode, and if 1, it
     * will be switched to blocking mode. This affects calls like
     * fgets and fread
     * that read from the stream. In non-blocking mode an
     * fgets call will always return right away
     * while in blocking mode it will wait for data to become available
     * on the stream.
     * </p>
     * @return bool Returns true on success or false on failure.
     */
    public function stream_set_blocking($stream, $mode)
    {
        return stream_set_blocking($stream, $mode);
    }

    /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Sets file buffering on the given stream
     * @link http://php.net/manual/en/function.stream-set-write-buffer.php
     * @param resource $stream <p>
     * The file pointer.
     * </p>
     * @param int $buffer <p>
     * The number of bytes to buffer. If buffer
     * is 0 then write operations are unbuffered. This ensures that all writes
     * with fwrite are completed before other processes are
     * allowed to write to that output stream.
     * </p>
     * @return int 0 on success, or EOF if the request cannot be honored.
     */
    public function stream_set_write_buffer($stream, $buffer)
    {
        return stream_set_write_buffer($stream, $buffer);
    }

    /**
     * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
     * Runs the equivalent of the select() system call on the given
      arrays of streams with a timeout specified by tv_sec and tv_usec
     * @link http://php.net/manual/en/function.stream-select.php
     * @param array $read <p>
     * The streams listed in the read array will be watched to
     * see if characters become available for reading (more precisely, to see if
     * a read will not block - in particular, a stream resource is also ready on
     * end-of-file, in which case an fread will return
     * a zero length string).
     * </p>
     * @param array $write <p>
     * The streams listed in the write array will be
     * watched to see if a write will not block.
     * </p>
     * @param array $except <p>
     * The streams listed in the except array will be
     * watched for high priority exceptional ("out-of-band") data arriving.
     * </p>
     * <p>
     * When stream_select returns, the arrays
     * read, write and
     * except are modified to indicate which stream
     * resource(s) actually changed status.
     * </p>
     * You do not need to pass every array to
     * stream_select. You can leave it out and use an
     * empty array or &null; instead. Also do not forget that those arrays are
     * passed by reference and will be modified after
     * stream_select returns.
     * @param int $tv_sec <p>
     * The tv_sec and tv_usec
     * together form the timeout parameter,
     * tv_sec specifies the number of seconds while
     * tv_usec the number of microseconds.
     * The timeout is an upper bound on the amount of time
     * that stream_select will wait before it returns.
     * If tv_sec and tv_usec are
     * both set to 0, stream_select will
     * not wait for data - instead it will return immediately, indicating the
     * current status of the streams.
     * </p>
     * <p>
     * If tv_sec is &null; stream_select
     * can block indefinitely, returning only when an event on one of the
     * watched streams occurs (or if a signal interrupts the system call).
     * </p>
     * <p>
     * Using a timeout value of 0 allows you to
     * instantaneously poll the status of the streams, however, it is NOT a
     * good idea to use a 0 timeout value in a loop as it
     * will cause your script to consume too much CPU time.
     * </p>
     * <p>
     * It is much better to specify a timeout value of a few seconds, although
     * if you need to be checking and running other code concurrently, using a
     * timeout value of at least 200000 microseconds will
     * help reduce the CPU usage of your script.
     * </p>
     * <p>
     * Remember that the timeout value is the maximum time that will elapse;
     * stream_select will return as soon as the
     * requested streams are ready for use.
     * </p>
     * @param int $tv_usec [optional] <p>
     * See tv_sec description.
     * </p>
     * @return int On success stream_select returns the number of
     * stream resources contained in the modified arrays, which may be zero if
     * the timeout expires before anything interesting happens. On error false
     * is returned and a warning raised (this can happen if the system call is
     * interrupted by an incoming signal).
     */
    function stream_select(&$readarray, &$writearray, &$exceptarray, $tv_sec, $tv_usec = null)
    {
        return stream_select($readarray, $writearray, $exceptarray, $tv_sec, $tv_usec);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Binary-safe file read
     * @link http://php.net/manual/en/function.fread.php
     * @param resource $handle &fs.file.pointer;
     * @param int $length <p>
     * Up to length number of bytes read.
     * </p>
     * @return string the read string &return.falseforfailure;.
     */
    function fread($handle, $length)
    {
        return @fread($handle, $length);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Binary-safe file write
     * @link http://php.net/manual/en/function.fwrite.php
     * @param resource $handle &fs.file.pointer;
     * @param string $string <p>
     * The string that is to be written.
     * </p>
     * @param int $length [optional] <p>
     * If the length argument is given, writing will
     * stop after length bytes have been written or
     * the end of string is reached, whichever comes
     * first.
     * </p>
     * <p>
     * Note that if the length argument is given,
     * then the magic_quotes_runtime
     * configuration option will be ignored and no slashes will be
     * stripped from string.
     * </p>
     * @return int
     */
    public function fwrite($handle, $string, $length = null)
    {
        return fwrite($handle, $string, $length);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Changes file mode
     * @link http://php.net/manual/en/function.chmod.php
     * @param string $filename <p>
     * Path to the file.
     * </p>
     * @param int $mode <p>
     * Note that mode is not automatically
     * assumed to be an octal value, so strings (such as "g+w") will
     * not work properly. To ensure the expected operation,
     * you need to prefix mode with a zero (0):
     * </p>
     * <p>
     * ]]>
     * </p>
     * <p>
     * The mode parameter consists of three octal
     * number components specifying access restrictions for the owner,
     * the user group in which the owner is in, and to everybody else in
     * this order. One component can be computed by adding up the needed
     * permissions for that target user base. Number 1 means that you
     * grant execute rights, number 2 means that you make the file
     * writeable, number 4 means that you make the file readable. Add
     * up these numbers to specify needed rights. You can also read more
     * about modes on Unix systems with 'man 1 chmod'
     * and 'man 2 chmod'.
     * </p>
     * <p>
     * @return bool Returns true on success or false on failure.
     */
    public function chmod($filename, $mode)
    {
        $oldUmask  = umask(0);
        $result = @chmod($filename, $mode);
        umask($oldUmask);

        return $result;
    }

    /**
     * Return all request headers
     * @return array
     */
    public function getallheaders()
    {
        $headers = array();

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Execute an external program and display raw output
     * @link http://php.net/manual/en/function.passthru.php
     * @param string $command <p>
     * The command that will be executed.
     * @return void
     */
    public function passthru($command)
    {
        $out = null;

        return @passthru($command, $out);
    }

    /**
     * (PHP 4, PHP 5)<br/>
     * Execute an external program
     * @link http://php.net/manual/en/function.exec.php
     * @param string $command <p>
     * The command that will be executed.
     * </p>
     * @param array $output [optional] <p>
     * If the <i>output</i> argument is present, then the
     * specified array will be filled with every line of output from the
     * command. Trailing whitespace, such as \n, is not
     * included in this array. Note that if the array already contains some
     * elements, <b>exec</b> will append to the end of the array.
     * If you do not want the function to append elements, call
     * <b>unset</b> on the array before passing it to
     * <b>exec</b>.
     * </p>
     * @param int $return_var [optional] <p>
     * If the <i>return_var</i> argument is present
     * along with the <i>output</i> argument, then the
     * return status of the executed command will be written to this
     * variable.
     * </p>
     * @return string The last line from the result of the command. If you need to execute a
     * command and have all the data from the command passed directly back without
     * any interference, use the <b>passthru</b> function.
     * </p>
     * <p>
     * To get the output of the executed command, be sure to set and use the
     * <i>output</i> parameter.
     */
    public function exec($command, array &$output = null, &$return_var = null)
    {
        return @exec($command, $output, $return_var);
    }
}