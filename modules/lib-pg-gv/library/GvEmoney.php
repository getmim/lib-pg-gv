<?php
/**
 * GvEmoney
 * @package lib-pg-gv
 * @version 0.0.1
 */

namespace LibPgGv\Library;


class GvEmoney
{
    protected static $_error;

    protected static function call(
        array $signs,
        string $uri,
        array $data,
        bool $json = true
    ): ?array {
        $res = Gv::call($signs, $uri, $data, $json);
        if (!$res) {
            return self::setError(Gv::lastError());
        }

        return $res;
    }

    protected static function encodePayload($payload)
    {
        if (preg_match('![^A-Za-z0-9+\/=]!', $payload)) {
            return base64_encode($payload);
        }

        return $payload;
    }

    protected static function setError($error)
    {
        self::$_error = $error;
        return null;
    }

    public static function lastError(): ?array
    {
        return self::$_error;
    }

    public static function BalanceInquiry(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['gv_connect_key'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/transaction/cekSaldo';

        return self::call($signs, $uri, $data, false);
    }

    public static function CheckPaymentQR(array $data): ?array
    {
        $signs = [
            '_custom',
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/qris/cekStatusIssuer';

        return self::call($signs, $uri, $data, false);
    }

    public static function CheckStatusTransaction(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['custom_cek'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/transaction/cekStatus';

        return self::call($signs, $uri, $data, false);
    }

    public static function DirectRegister(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['username'],
            $data['name'],
            $data['email'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/auth/direct_register';

        return self::call($signs, $uri, $data, false);
    }

    public static function DisconnectAccount(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['gv_connect_key'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/auth/disconnect';

        return self::call($signs, $uri, $data, false);
    }

    public static function InquiryAccount(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['type'],
            $data['username'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/auth/inquiryAccount';

        return self::call($signs, $uri, $data, false);
    }

    public static function InquiryQR(array $data): ?array
    {
        $data['payload'] = self::encodePayload($data['payload']);

        $signs = [
            '_custom',
            $data['gv_connect_key'],
            $data['payload'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/v2/qris/qr_issuer_inquiry';

        return self::call($signs, $uri, $data);
    }

    public static function InquiryUsername(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['username'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/transaction/cekUsername';

        return self::call($signs, $uri, $data, false);
    }

    public static function PaymentQR(array $data): ?array
    {
        $data['payload'] = self::encodePayload($data['payload']);

        $signs = [
            '_custom',
            $data['gv_connect_key'],
            $data['payload'],
            $data['amount'],
            $data['tip'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/v2/qris/qr_issuer_payment';

        return self::call($signs, $uri, $data);
    }

    public static function TransactionHistory(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['gv_connect_key'],
            $data['date_from'],
            $data['date_to'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/transaction/historyTransaction';

        return self::call($signs, $uri, $data, false);
    }

    public static function TransferFund(array $data): ?array
    {
        $signs = [
            '_custom',
            $data['username_to'],
            $data['gv_connect_key_from'],
            $data['amount'],
            '_merchant_key'
        ];

        $uri = '/payment_channel/gv_connect/transaction/transferSaldoInterUser';

        return self::call($signs, $uri, $data, false);
    }
}
