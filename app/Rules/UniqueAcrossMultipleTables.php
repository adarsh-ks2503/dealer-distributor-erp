<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueAcrossMultipleTables implements ValidationRule
{
    protected $tables;
    protected $column;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function __construct(array $tables, $column = null)
    {
        $this->tables = $tables;
        $this->column = $column;
    }

    public function passes($attribute, $value)
    {
        // Agar value empty hai, toh uniqueness check skip karo
        if (empty($value)) {
            return true;
        }

        $column = $this->column ?? explode('.', $attribute)[count(explode('.', $attribute)) - 1];

        foreach ($this->tables as $table) {
            if (DB::table($table)->where($column, $value)->exists()) {
                return false; // Value mil gayi, validation fail
            }
        }

        return true; // Value kisi bhi table mein nahi mili, validation pass
    }

    public function message()
    {
        return 'The :attribute has already been taken.';
    }
    
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
}
