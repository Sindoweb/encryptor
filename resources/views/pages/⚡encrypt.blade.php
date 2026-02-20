<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Encrypt & Hash')] class extends Component {
    public string $text = '';

    /** @var array<int, array{category: string, algorithm: string, output: string}> */
    public array $results = [];

    public function encrypt(): void
    {
        $this->validate([
            'text' => ['required', 'string'],
        ]);

        $this->results = [];

        // $this->addHashAlgorithms();
        $this->addPasswordHashes();
        // $this->addEncodings();
        // $this->addHmacHashes();
    }

    private function addHashAlgorithms(): void
    {
        foreach (hash_algos() as $algo) {
            $this->results[] = [
                'category' => 'Hash',
                'algorithm' => $algo,
                'output' => hash($algo, $this->text),
            ];
        }
    }

    private function addPasswordHashes(): void
    {
        $this->results[] = [
            'category' => 'Password',
            'algorithm' => 'bcrypt',
            'output' => password_hash($this->text, PASSWORD_BCRYPT),
        ];

        if (defined('PASSWORD_ARGON2I')) {
            $this->results[] = [
                'category' => 'Password',
                'algorithm' => 'argon2i',
                'output' => password_hash($this->text, PASSWORD_ARGON2I),
            ];
        }

        if (defined('PASSWORD_ARGON2ID')) {
            $this->results[] = [
                'category' => 'Password',
                'algorithm' => 'argon2id',
                'output' => password_hash($this->text, PASSWORD_ARGON2ID),
            ];
        }
    }

    private function addEncodings(): void
    {
        $this->results[] = [
            'category' => 'Encoding',
            'algorithm' => 'base64',
            'output' => base64_encode($this->text),
        ];

        $this->results[] = [
            'category' => 'Encoding',
            'algorithm' => 'url_encode',
            'output' => urlencode($this->text),
        ];

        $this->results[] = [
            'category' => 'Encoding',
            'algorithm' => 'hex (bin2hex)',
            'output' => bin2hex($this->text),
        ];

        $this->results[] = [
            'category' => 'Encoding',
            'algorithm' => 'rot13',
            'output' => str_rot13($this->text),
        ];

        $this->results[] = [
            'category' => 'Encoding',
            'algorithm' => 'Laravel encrypt (AES-256-CBC)',
            'output' => encrypt($this->text),
        ];
    }

    private function addHmacHashes(): void
    {
        $key = config('app.key');

        $this->results[] = [
            'category' => 'HMAC',
            'algorithm' => 'hmac-sha256',
            'output' => hash_hmac('sha256', $this->text, $key),
        ];

        $this->results[] = [
            'category' => 'HMAC',
            'algorithm' => 'hmac-sha512',
            'output' => hash_hmac('sha512', $this->text, $key),
        ];
    }
};
?>

<div class="mx-auto max-w-5xl px-4 py-12">
    <div class="mb-8">
        <flux:heading size="xl">Encrypt & Hash</flux:heading>
        <flux:text class="mt-2">Enter text below and see it encrypted, hashed, and encoded using every available PHP algorithm.</flux:text>
    </div>

    <form wire:submit="encrypt" class="mb-8 flex items-end gap-4">
        <div class="flex-1">
            <flux:input wire:model="text" label="Text to encrypt" placeholder="Enter your text here..." />
        </div>

        <flux:button type="submit" variant="primary">Encrypt</flux:button>
    </form>

    @if (count($results))
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Category</flux:table.column>
                <flux:table.column>Algorithm</flux:table.column>
                <flux:table.column>Output</flux:table.column>
                <flux:table.column class="w-16"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($results as $result)
                    <flux:table.row :key="$loop->index">
                        <flux:table.cell>
                            <flux:badge size="sm" inset="top bottom" :color="match($result['category']) {
                                'Hash' => 'blue',
                                'Password' => 'purple',
                                'Encoding' => 'amber',
                                'HMAC' => 'green',
                                default => 'zinc',
                            }">{{ $result['category'] }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell variant="strong">{{ $result['algorithm'] }}</flux:table.cell>
                        <flux:table.cell class="max-w-md">
                            <span class="block truncate font-mono text-xs">{{ $result['output'] }}</span>
                        </flux:table.cell>
                        <flux:table.cell>
                            <button
                                type="button"
                                x-data="{ copied: false }"
                                data-copy="{{ $result['output'] }}"
                                x-on:click="
                                    const textarea = document.createElement('textarea');
                                    textarea.value = $el.dataset.copy;
                                    textarea.style.position = 'fixed';
                                    textarea.style.opacity = '0';
                                    document.body.appendChild(textarea);
                                    textarea.select();
                                    document.execCommand('copy');
                                    document.body.removeChild(textarea);
                                    copied = true;
                                    setTimeout(() => copied = false, 2000);
                                "
                                class="inline-flex items-center justify-center rounded-md p-1.5 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 dark:hover:text-zinc-200 dark:hover:bg-zinc-700 transition"
                            >
                                <flux:icon.clipboard-document x-show="!copied" class="size-4" />
                                <flux:icon.check x-cloak x-show="copied" class="size-4 text-green-500" />
                            </button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</div>
