<?php

namespace App\NativeComponents;

use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class History extends NativeComponent
{
    public function history(): array
    {
        return array_reverse(session('analysis_history', []));
    }

    public function parsedLines(?string $result): array
    {
        if ($result === null || $result === '') {
            return [];
        }

        $rows = [];

        foreach (explode("\n", $result) as $line) {
            $line = str_replace(['*', '-'], '', $line);

            $parts = explode(':', $line, 2);

            if (count($parts) !== 2) {
                continue;
            }

            $label = trim($parts[0]);
            $value = trim($parts[1]);

            if (str_contains(strtolower($label), 'title')) {
                $items = array_values(array_filter(array_map(
                    fn ($t) => trim($t),
                    explode(',', $value)
                )));
            } else {
                $items = [];
            }

            $rows[] = [
                'label' => $label,
                'value' => $value,
                'items' => $items,
            ];
        }

        return $rows;
    }

    public function navTitle(): string
    {
        return 'History';
    }

    public function render(): View
    {
        return view('native.history');
    }
}
