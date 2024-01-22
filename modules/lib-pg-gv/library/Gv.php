<?php
/**
 * Gv
 * @package lib-pg-gv
 * @version 0.0.1
 */

namespace LibPgGv\Library;

use LibCurl\Library\Curl;

class Gv
{
    protected static $_error;
    protected static $_custom;

    protected static function makeError(
        string $code = '01',
        string $message = 'Failed',
        array $data = []
    ) {
        self::$_error = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        return null;
    }

    protected static function prepareBody(array $body, string $signature): array
    {
        $config = \Mim::$app->config->libPgGv;
        $body['merchant_id'] = $config->merchant->id;
        $body['custom'] = self::makeCustom(false);
        $body['signature'] = $signature;

        return $body;
    }

    public static function lastError(): ?array
    {
        return self::$_error;
    }

    public static function makeCustom(bool $reset = false)
    {
        if ($reset) {
            self::$_custom = null;
        }

        if (self::$_custom) {
            return self::$_custom;
        }

        $config = \Mim::$app->config->libPgGv;
        $custom_key = [
            $config->merchant->id,
            uniqid(),
            uniqid()
        ];

        $custom_key = implode('/', $custom_key);

        self::$_custom = 'MIM-' . strtoupper(md5($custom_key));

        return self::$_custom;
    }

    public static function makeSignature(array $rules)
    {
        $config = \Mim::$app->config->libPgGv;

        $signs = [];
        foreach ($rules as $rule) {
            if ($rule === '_custom') {
                $signs[] = self::makeCustom(true);
            } elseif ($rule == '_merchant_key') {
                $signs[] = $config->merchant->key;
            } else {
                $signs[] = $rule;
            }
        }

        $sign_key = [
            $config->merchant->id,
            md5(implode('', $signs))
        ];

        return md5(implode('', $sign_key));
    }

    public static function call(array $signs, string $uri, array $body, bool $json = true): ?array
    {
        $config = \Mim::$app->config->libPgGv;

        $signature = self::makeSignature($signs);
        $body = self::prepareBody($body, $signature);

        $opt = [
            'url' => $config->base . $uri,
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => $body,
            'agent' => 'Mim/LibPgGv',
            'timeout' => 30
        ];

        if ($json) {
            $opt['headers']['Content-Type'] = 'application/json';
        }

        $res = Curl::fetch($opt);

        if (!is_object($res)) {
            return self::makeError();
        }

        if (!isset($res->respondcode)) {
            return self::makeError();
        }

        if ($res->respondcode != '00') {
            return self::makeError(
                $res->respondcode,
                $res->respondmsg ?? 'Unknow Error Message',
                (array)($res->responddata ?? [])
            );
        }

        return (array)$res->responddata;
    }
}
