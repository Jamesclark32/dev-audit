<?php

namespace JamesClark32\DevAudit\Helpers;

class OutputFormatHelper
{
    public function buildOutput(string $output, ?string $textColor = 'white', ?array $options = []): string
    {
        $optionsString = '';
        if (count($options) > 0) {
            $optionsString = ';options='.implode(',', $options);
        }
        $string = '<fg={{ textColor }}{{ $options }}>{{ output }}</>'.PHP_EOL;

        return strtr($string, [
            '{{ textColor }}' => $textColor,
            '{{ output }}' => $output,
            '{{ $options }}' => $optionsString,
        ]);
    }

    public function buildInlineOutput(string $output, ?string $textColor = 'white', ?array $options = []): string
    {
        $optionsString = '';
        if (count($options) > 0) {
            $optionsString = ';options='.implode(',', $options);
        }
        $string = '<fg={{ textColor }}{{ $options }}>{{ output }}</>';

        return strtr($string, [
            '{{ textColor }}' => $textColor,
            '{{ output }}' => $output,
            '{{ $options }}' => $optionsString,
        ]);
    }
}
