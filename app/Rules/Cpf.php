<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', (string) $value);

        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            $fail('O CPF deve ter 11 dígitos.');
            return;
        }

        // Verifica se todos os dígitos são iguais (ex: 111.111.111-11), o que é inválido
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $fail('O CPF informado não é válido.');
            return;
        }

        // Calcula o primeiro dígito verificador
        for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--) {
            $soma += $cpf[$i] * $j;
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;

        // Calcula o segundo dígito verificador
        for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--) {
            $soma += $cpf[$i] * $j;
        }
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;

        // Verifica se os dígitos calculados batem com os dígitos do CPF
        if ($dv1 != $cpf[9] || $dv2 != $cpf[10]) {
            $fail('O CPF informado não é válido.');
        }
    }
}
