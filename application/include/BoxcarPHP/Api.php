<?php
/**
 * Boxcar client api for providers.
 *
 * History:
 *
 *		29-Nov-10
 *			First version, well second version as I mv'd the sample
 *			file over the client. So this is a re-write. Doh!
 *
 * @author Russell Smith <russell.smith@ukd1.co.uk>
 * @copyright UKD1 Limited 2010
 * @license licence.txt ISC license
 * @see http://boxcar.io/help/api/providers
 * @see https://github.com/ukd1/Boxcar
 */
class BoxcarPHP_Api {

    /**
     * The useragent to send though
     */
    const USERAGENT = 'PHP';

    /**
     * The endpoint for service.
     */
    const ENDPOINT = 'https://yellow2.process-one.net/api/push/';

    /**
     * Timeout for the API requests in seconds
     */
    const TIMEOUT = 5;

    /**
     * Stores the api key
     *
     * @var string
     */
    private $api_key;

    /**
     * Stores the api secret
     *
     * @var string
     */
    private $secret;

    /**
     * A default icon url
     *
     * @var string
     */
    private $default_icon_url;

    /**
     * Make a new instance of the API client
     *
     * @param string $api_key your api key
     * @param string $secret your api secret
     * @param string $default_icon_url url to a 57x57 icon to use with a message
     */
    public function __construct ($api_key, $secret, $default_icon_url = null) {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->default_icon_url = $default_icon_url;
    }

    /**
     * Get a new instance of the API client
     *
     * @param string $api_key your api key
     * @param string $secret your api secret
     * @param string $default_icon_url url to a 57x57 icon to use with a message
     */
    public static function factory ($api_key, $secret, $default_icon = null) {
        return new self($api_key, $secret, $default_icon);
    }

    /**
     * Send a notification to all users of your provider
     *
     * @param string $name the name of the sender
     * @param string $message the message body
     * @param string $id an optional unique id, will stop the same message getting sent twice
     * @param string $payload Optional; The payload to be passed in as part of the redirection URL.
     *                        Keep this as short as possible. If your redirection URL contains "::user::" in it,
     *                        this will replace it in the URL. An example payload would be the users username, to
     *                        take them to the appropriate page when redirecting
     * @param string $source_url Optional; This is a URL that may be used for future devices. It will replace the redirect payload.
     * @param string $icon  Optional; This is the URL of the icon that will be shown to the user. Standard size is 57x57.
     */
    public function broadcast ($message, $badge, $tokens_apple, $tokens_android, $task_id) {
        return $this->do_notify($message, $badge, $tokens_apple, $tokens_android, $task_id);
    }


    /**
     * Internal function for actually sending the notifications
     *
     * @param string $name the name of the sender
     * @param string $message the message body
     * @param string $id an optional unique id, will stop the same message getting sent twice
     * @param string $payload Optional; The payload to be passed in as part of the redirection URL.
     *                        Keep this as short as possible. If your redirection URL contains "::user::" in it,
     *                        this will replace it in the URL. An example payload would be the users username, to
     *                        take them to the appropriate page when redirecting
     * @param string $source_url Optional; This is a URL that may be used for future devices. It will replace the redirect payload.
     * @param string $icon Optional; This is the URL of the icon that will be shown to the user. Standard size is 57x57.
     */
    private function do_notify($message, $badge, $tokens_apple, $tokens_android, $task_id) {
        $notification = array(
            'aps' => array(
                'badge' => 'auto',
                'alert' => $message,
            ),
        );

        $notification['device_tokens'] = array();

        if (!empty($tokens_apple)) {
            $notification['device_tokens'] = $tokens_apple;
        }

        if (!empty($tokens_android)) {
            $notification['device_tokens'] = array_merge($notification['device_tokens'], $tokens_android);
        }

        $result = $this->http_post($this->api_key, $this->secret, $notification);

        return $this->default_response_handler($result);
    }

    /**
     * Correctly handle the error / success states from the boxcar servers
     *
     * @see http://boxcar.io/help/api/providers
     * @param array $result
     * @return string
     */
    private function default_response_handler ($result) {

        // work out what to do based on http code
        switch ($result['http_code']) {
            case 200:
                // return true, currently there are no responses returning anything...
                return true;
                break;

            // HTTP status code of 400, it is because you failed to send the proper parameters
            case 400:
                throw new BoxcarPHP_Exception('Incorrect parameters passed', $result['http_code']);
                break;

            // For request failures, you will receive either HTTP status 403 or 401.

            // HTTP status code 401's, it is because you are passing in either an invalid token,
            // or the user has not added your service. Also, if you try and send the same notification
            // id twice.
            case 401:
                throw new BoxcarPHP_Exception('Request failed (Probably your fault)', $result['http_code']);
                break;

            case 403:
                throw new BoxcarPHP_Exception('Request failed (General)', $result['http_code']);
                break;

            // Unkown code
            default:
                throw new BoxcarPHP_Exception('Unknown response', $result['http_code']);
                break;
        }
    }

    /**
     * HTTP POST a specific task with the supplied data
     *
     * @param string $task
     * @param array $data
     * @return array
     */
    private function http_post ($access_key, $secret_key, $data) {
        $post_data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::ENDPOINT);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_USERPWD, "$access_key:$secret_key");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

        $result = curl_exec ($ch);

        $tmp = curl_getinfo($ch);
        $tmp['result'] = $result;
        curl_close ($ch);

        return $tmp;
    }
}
