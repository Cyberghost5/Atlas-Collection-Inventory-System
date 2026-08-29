<?php

namespace App\Services;

class BarcodeService
{
    /**
     * Code128 Pattern Dictionary (Code B / Code C)
     */
    private static $code128Patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', // 0-9
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', // 10-19
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', // 20-29
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', // 30-39
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', // 40-49
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', // 50-59
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', // 60-69
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', // 70-79
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', // 80-89
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', // 90-99
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112'                                // 100-106 (106 is STOP)
    ];

    /**
     * Generate Code128 SVG Barcode string
     */
    public static function getCode128Svg(string $text, int $height = 40, float $moduleWidth = 1.5): string
    {
        $text = strtoupper(trim($text));
        if (empty($text)) {
            $text = 'ATL-000';
        }

        // Use Code B Start (index 104)
        $codeValue = 104;
        $checksum = $codeValue;
        $patterns = [self::$code128Patterns[104]];

        $pos = 1;
        for ($i = 0; $i < strlen($text); $i++) {
            $char = $text[$i];
            $ascii = ord($char);
            $val = $ascii - 32; // Code B value mapping
            if ($val < 0 || $val > 95) {
                $val = 0;
            }
            $checksum += $val * $pos;
            $patterns[] = self::$code128Patterns[$val];
            $pos++;
        }

        // Calculate Checksum & Add Stop Symbol
        $checkVal = $checksum % 103;
        $patterns[] = self::$code128Patterns[$checkVal];
        $patterns[] = self::$code128Patterns[106]; // STOP

        // Convert patterns string to bar widths
        $combinedPattern = implode('', $patterns);
        $totalUnits = 0;
        for ($k = 0; $k < strlen($combinedPattern); $k++) {
            $totalUnits += (int)$combinedPattern[$k];
        }

        $svgWidth = ceil($totalUnits * $moduleWidth) + 10;
        $x = 5;

        $rects = [];
        $isBar = true;
        for ($k = 0; $k < strlen($combinedPattern); $k++) {
            $w = (int)$combinedPattern[$k] * $moduleWidth;
            if ($isBar) {
                $rects[] = sprintf('<rect x="%.2f" y="0" width="%.2f" height="%d" fill="#000000"/>', $x, $w, $height);
            }
            $x += $w;
            $isBar = !$isBar;
        }

        $rectsSvg = implode('', $rects);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$svgWidth} {$height}" width="100%" height="{$height}px" shape-rendering="crispEdges">
    {$rectsSvg}
</svg>
SVG;
    }
}
