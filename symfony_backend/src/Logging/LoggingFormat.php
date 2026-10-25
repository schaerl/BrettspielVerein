<?php

namespace App\Logging;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;
use Override;

class LoggingFormat implements FormatterInterface
{

    private readonly int $PAD_SIZE;
    private readonly LineFormatter $outDelegate;

    public function __construct()
    {
        $this->PAD_SIZE = 40;
        $dateFormat = 'Y-m-d\TH:i:s.v';
        $format = "%datetime% [%extra.padded_level%] %extra.class%: %message% %context%\n";
        $this->outDelegate = new LineFormatter($format, $dateFormat);
    }

    #[Override]
    public function format(LogRecord $record): string
    {
        $record->extra['class'] = str_pad(substr($record->extra['class'], -1 * $this->PAD_SIZE), $this->PAD_SIZE, ' ', STR_PAD_RIGHT);
        $record->extra['padded_level'] = str_pad($record->level->getName(), 7, ' ', STR_PAD_RIGHT);
        return $this->outDelegate->format($record);
    }

    #[Override]
    public function formatBatch(array $records): string
    {
        $message = '';
        foreach ($records as $record) {
            $message .= $this->format($record);
        }

        return $message;
    }
}
