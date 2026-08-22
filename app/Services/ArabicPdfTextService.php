<?php

namespace App\Services;

class ArabicPdfTextService
{
    private const FORMS = '{"\u0621":["\uFE80"],"\u0622":["\uFE81","\uFE82"],"\u0623":["\uFE83","\uFE84"],"\u0624":["\uFE85","\uFE86"],"\u0625":["\uFE87","\uFE88"],"\u0626":["\uFE89","\uFE8A","\uFE8B","\uFE8C"],"\u0627":["\uFE8D","\uFE8E"],"\u0628":["\uFE8F","\uFE90","\uFE91","\uFE92"],"\u0629":["\uFE93","\uFE94"],"\u062A":["\uFE95","\uFE96","\uFE97","\uFE98"],"\u062B":["\uFE99","\uFE9A","\uFE9B","\uFE9C"],"\u062C":["\uFE9D","\uFE9E","\uFE9F","\uFEA0"],"\u062D":["\uFEA1","\uFEA2","\uFEA3","\uFEA4"],"\u062E":["\uFEA5","\uFEA6","\uFEA7","\uFEA8"],"\u062F":["\uFEA9","\uFEAA"],"\u0630":["\uFEAB","\uFEAC"],"\u0631":["\uFEAD","\uFEAE"],"\u0632":["\uFEAF","\uFEB0"],"\u0633":["\uFEB1","\uFEB2","\uFEB3","\uFEB4"],"\u0634":["\uFEB5","\uFEB6","\uFEB7","\uFEB8"],"\u0635":["\uFEB9","\uFEBA","\uFEBB","\uFEBC"],"\u0636":["\uFEBD","\uFEBE","\uFEBF","\uFEC0"],"\u0637":["\uFEC1","\uFEC2","\uFEC3","\uFEC4"],"\u0638":["\uFEC5","\uFEC6","\uFEC7","\uFEC8"],"\u0639":["\uFEC9","\uFECA","\uFECB","\uFECC"],"\u063A":["\uFECD","\uFECE","\uFECF","\uFED0"],"\u0641":["\uFED1","\uFED2","\uFED3","\uFED4"],"\u0642":["\uFED5","\uFED6","\uFED7","\uFED8"],"\u0643":["\uFED9","\uFEDA","\uFEDB","\uFEDC"],"\u0644":["\uFEDD","\uFEDE","\uFEDF","\uFEE0"],"\u0645":["\uFEE1","\uFEE2","\uFEE3","\uFEE4"],"\u0646":["\uFEE5","\uFEE6","\uFEE7","\uFEE8"],"\u0647":["\uFEE9","\uFEEA","\uFEEB","\uFEEC"],"\u0648":["\uFEED","\uFEEE"],"\u0649":["\uFEEF","\uFEF0"],"\u064A":["\uFEF1","\uFEF2","\uFEF3","\uFEF4"],"\u0671":["\uFB50","\uFB51"],"\u067E":["\uFB56","\uFB57","\uFB58","\uFB59"],"\u0686":["\uFB7A","\uFB7B","\uFB7C","\uFB7D"],"\u0698":["\uFB8A","\uFB8B"],"\u06A9":["\uFB8E","\uFB8F","\uFB90","\uFB91"],"\u06AF":["\uFB92","\uFB93","\uFB94","\uFB95"],"\u06CC":["\uFBFC","\uFBFD","\uFBFE","\uFBFF"]}';

    public function shape(?string $value): ?string
    {
        if ($value === null || ! preg_match('/[\x{0621}-\x{06FF}]/u', $value)) {
            return $value;
        }

        return preg_replace_callback(
            '/[\x{0621}-\x{06CC}\x{064B}-\x{065F}]+(?:\s+[\x{0621}-\x{06CC}\x{064B}-\x{065F}]+)*/u',
            fn (array $match) => $this->shapeSegment($match[0]),
            $value
        );
    }

    private function shapeSegment(string $segment): string
    {
        $forms = json_decode(self::FORMS, true, flags: JSON_THROW_ON_ERROR);
        $characters = preg_split('//u', $segment, -1, PREG_SPLIT_NO_EMPTY);
        $shaped = [];

        foreach ($characters as $index => $character) {
            if (! isset($forms[$character])) {
                $shaped[] = $character;

                continue;
            }

            $previous = $this->previousLetter($characters, $index, $forms);
            $next = $this->nextLetter($characters, $index, $forms);
            $joinsPrevious = $previous !== null && count($forms[$character]) > 1 && count($forms[$previous]) > 2;
            $joinsNext = $next !== null && count($forms[$character]) > 2 && count($forms[$next]) > 1;
            $variants = $forms[$character];

            $shaped[] = $joinsPrevious && $joinsNext ? $variants[3] : ($joinsPrevious ? $variants[1] : ($joinsNext ? $variants[2] : $variants[0]));
        }

        return implode('', array_reverse($shaped));
    }

    private function previousLetter(array $characters, int $index, array $forms): ?string
    {
        for ($position = $index - 1; $position >= 0; $position--) {
            if (isset($forms[$characters[$position]])) {
                return $characters[$position];
            }

            if (! preg_match('/[\x{064B}-\x{065F}]/u', $characters[$position])) {
                return null;
            }
        }

        return null;
    }

    private function nextLetter(array $characters, int $index, array $forms): ?string
    {
        for ($position = $index + 1, $length = count($characters); $position < $length; $position++) {
            if (isset($forms[$characters[$position]])) {
                return $characters[$position];
            }

            if (! preg_match('/[\x{064B}-\x{065F}]/u', $characters[$position])) {
                return null;
            }
        }

        return null;
    }
}