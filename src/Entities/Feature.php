<?php

declare(strict_types = 1);

namespace Forceedge01\BDDStaticAnalyserRules\Entities;

use Forceedge01\BDDStaticAnalyserRules\Processor;

class Feature
{
    public function __construct(int $lineNumber, array $content)
    {
        $this->lineNumber = $lineNumber;
        $this->content = $content;
    }

    public function getTags(): array
    {
        if (! isset($this->content[0])) {
            return [];
        }

        preg_match('/^@.*/', $this->content[0], $matches);
        if (count($matches) > 0) {
            return Processor\ArrayProcessor::cleanArray(explode(' ', trim($this->content[0])));
        }

        return [];
    }

    public function getNarrative(): array
    {
        $featureIndex = Processor\ArrayProcessor::getIndexMatching('/^Feature:/', $this->content);

        if ($featureIndex === null) {
            return [];
        }

        return Processor\ArrayProcessor::cleanArray(array_slice($this->content, $featureIndex+1));
    }

    public function getDescription(): string
    {
        $description = Processor\ArrayProcessor::getContentMatching('/^Feature:/', $this->content);

        return trim(str_replace('Feature:', $description));
    }
}
