<?php

declare(strict_types = 1);

namespace Forceedge01\BDDStaticAnalyserRules\Entities;

class Step
{
    public function __construct(int $lineNumber, string $title, array $table = [], array $pyString = [])
    {
        $this->lineNumber = $lineNumber;
        $this->title = $title;
        $this->trimmedTitle = trim($title);
        $this->table = $table;
        $this->parameters = $this->extractParameters();
        $this->pyString = $pyString;
    }

    public function getPyString(): array
    {
        return $this->pyString;
    }

    public function getTitle(): string
    {
        return trim($this->getRawTitle());
    }

    public function getRawTitle(): string
    {
        return $this->title;
    }

    public function getStepDefinition(): string
    {
        // Remove keyword and space.
        $filtered = trim(preg_replace('/^#?\s*(given|when|then|and|but)/i', '', $this->trimmedTitle));

        // Remove params.
        return preg_replace(['/\d+/i', '/"([^"]*)"/is'], ['{num}', '"{string}"'], $filtered);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function extractParameters($quote = '"'): array
    {
        $pattern = "/$quote([^$quote]*)$quote/";
        preg_match_all($pattern, $this->trimmedTitle, $matches);

        return $matches[1];
    }

    public function getKeyword(): string
    {
        $match =[];
        preg_match('/^#?\s*(given|when|then|and|but)/i', $this->trimmedTitle, $match);

        if (!isset($match[0])) {
            throw new \Exception("Step '{$this->trimmedTitle}' does not have a keyword.");
        }

        return strtolower($match[0]);
    }

    public function isActive(): bool
    {
        if (strpos($this->trimmedTitle, '#') === 0 || strpos($this->trimmedTitle, '//') === 0) {
            return false;
        }

        return true;
    }
}
