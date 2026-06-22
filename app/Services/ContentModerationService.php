<?php

namespace App\Services;

use App\Models\AllowedWord;

class ContentModerationService
{
    /**
     * Check if the content passes the blocklist filter.
     * Returns true if the content is allowed (no blocked words found).
     * If the blocklist is empty, all content is allowed.
     */
    public function isAllowed(string $content): bool
    {
        $blockedWords = AllowedWord::pluck('word')->toArray();

        if (empty($blockedWords)) {
            return true;
        }

        $contentLower = mb_strtolower($content);

        foreach ($blockedWords as $word) {
            $wordLower = mb_strtolower(trim($word));
            if (!empty($wordLower) && mb_strpos($contentLower, $wordLower) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the list of blocked words found in the content.
     */
    public function getBlockedWords(string $content): array
    {
        $blockedWords = AllowedWord::pluck('word')->toArray();
        $found = [];

        if (empty($blockedWords)) {
            return $found;
        }

        $contentLower = mb_strtolower($content);

        foreach ($blockedWords as $word) {
            $wordLower = mb_strtolower(trim($word));
            if (!empty($wordLower) && mb_strpos($contentLower, $wordLower) !== false) {
                $found[] = $word;
            }
        }

        return $found;
    }
}
