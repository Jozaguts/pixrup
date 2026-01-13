<?php
namespace  App\Domain\PixHunt\Concerns;

use JsonException;

trait HandlesRuneDefaults {
    /**
     * Default rune version
     */
    protected function defaultRuneVersion(): string
    {
        return property_exists($this, 'runeVersion') ? $this->runeVersion : 'v1.0.0';
    }

    protected function computedAt(): string
    {
        return now()->toDateTimeString();
    }

    abstract protected function propertyId(): int;

    abstract protected function runeName(): string;

    /**
     * @throws JsonException
     */
    public function toArray(): array
    {
        $payload = $this->runePayload();
        // Ensure rune_value is JSON-encoded if it's an array
        $encodedValue = is_array($payload['rune_value'])
            ? json_encode($payload['rune_value'], JSON_THROW_ON_ERROR)
            : $payload['rune_value'];

        return [
            ...$payload,
            'property_id' => $this->propertyId(),
            'rune_key'    => $this->runeName(),
            'computed_at' => $this->computedAt(),
            'version'     => $this->defaultRuneVersion(),
            'rune_value'  => $encodedValue,
            'provider'    => $this->runeProvider(),
        ];
    }

    abstract protected function runePayload(): array;

    abstract protected function runeProvider(): string;
}
