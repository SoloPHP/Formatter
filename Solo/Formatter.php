<?php

namespace Solo;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class Formatter
{
    private array $defaults;

    public function __construct(array $defaults = [])
    {
        $this->defaults = $defaults;
    }

    public function phone(string $phone, array $options = []): string
    {
        if (!PhoneNumberUtil::isViablePhoneNumber($phone)) {
            return $phone;
        }

        $defaults = [
            'region' => $this->defaults['phone']['region'] ?? 'US',
            'format' => $this->defaults['phone']['format'] ?? 'E164',
        ];
        $options = array_merge($defaults, $options);

        $region = strtoupper($options['region']);
        $format = strtoupper($options['format']);

        $formatMap = [
            'E164' => PhoneNumberFormat::E164,
            'INTERNATIONAL' => PhoneNumberFormat::INTERNATIONAL,
            'NATIONAL' => PhoneNumberFormat::NATIONAL,
            'RFC3966' => PhoneNumberFormat::RFC3966,
        ];

        $phoneFormat = $formatMap[$format] ?? PhoneNumberFormat::E164;

        $phoneUtil = PhoneNumberUtil::getInstance();
        $numberProto = $phoneUtil->parse($phone, $region);
        return $phoneUtil->format($numberProto, $phoneFormat);

    }

    public function money(float $amount, array $options = []): string
    {
        $defaults = [
            'sign' => $this->defaults['money']['sign'] ?? '$',
            'before' => $this->defaults['money']['before'] ?? true,
            'decimals' => $this->defaults['money']['decimals'] ?? 2,
            'decimal_separator' => $this->defaults['money']['decimal_separator'] ?? ',',
            'thousands_separator' => $this->defaults['money']['thousands_separator'] ?? ' ',
        ];
        $options = array_merge($defaults, $options);

        $formattedAmount = number_format($amount, $options['decimals'], $options['decimal_separator'], $options['thousands_separator']);

        return $options['before'] ? "{$options['sign']} $formattedAmount" : "$formattedAmount {$options['sign']}";
    }
}