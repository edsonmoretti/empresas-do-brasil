<?php

function formatCnpj(string $cnpj): string
{
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
    return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
}

function formatCpf(string $cpf): string
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}
