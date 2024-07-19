<?php

namespace App\Services;

class Censurator
{
    public function censor(string $text) : string
    {
        $interdits = [
            'homophobie', 'homophobe', 'haine contre les homosexuels', 'pd',
            'gouine', 'lgbtq', 'lgbt', 'homosexuel', 'bisexuel', 'transgenre',
            'queer', 'dépression', 'déprimé', 'tristesse', 'mal-être', 'suicide',
            'con', 'conne', 'connasse', 'pute', 'encule'
        ];

        foreach ($interdits as $interdit) {
            // Utiliser une regex insensible à la casse pour trouver toutes les occurrences
            $pattern = '/' . preg_quote($interdit, '/') . '/i';

            // Utiliser une fonction de rappel pour conserver la casse d'origine
            $text = preg_replace_callback($pattern, function($matches) {
                return str_repeat('*', strlen($matches[0]));
            }, $text);
        }

        return $text;
    }

}