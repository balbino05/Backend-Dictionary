<?php

namespace App\Services\Contracts;

interface DictionaryServiceInterface
{
    // Defina os métodos necessários para o serviço de dicionário
    public function searchWords($search, $limit = 10, $page = 1);
    public function getWordDefinition($word);
}
