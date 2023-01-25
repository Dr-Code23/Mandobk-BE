<?php

namespace App\Http\Requests\Api\Web\V1\Categories;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Foundation\Http\FormRequest;

class webCategoriesRequest extends FormRequest
{
    use translationTrait;
    use HttpResponse;
    private string $file_name = 'Categories/categoriesTranslationFile.';
    protected $stopOnFirstFailure = false;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'commercial_name' => config('validationRules.categories_commercial_name'),
            'scientefic_name' => config('validationRules.categories_scientefic_name'),
            'quantity' => config('validationRules.int'),
            'purchase_price' => config('validationRules.double'),
            'selling_price' => config('validationRules.double'),
            'bonus' => config('validationRules.double'),
            'concentrate' => config('validationRules.double'),
            'patch_number' => config('validationRules.patch_number'),
            'provider' => config('validationRules.provider'),
            'entry_date' => config('validationRules.date'),
            'expire_date' => config('validationRules.date'),
        ];
    }

    public function messages()
    {
        $ar = [
            'quantity.numeric' => $this->translateErrorMessage($this->file_name.'quantity', 'numeric'),
            'quantity.min' => $this->translateErrorMessage($this->file_name.'quantity', 'min.numeric'),
        ];
        foreach (['commercial_name', 'scientefic_name', 'selling_price', 'purchase_price', 'concentrate', 'bonus', 'patch_number', 'provider', 'entry_date', 'expire_date'] as $i) {
            foreach (['required'] as $j) {
                $ar["$i.$j"] = $this->translateErrorMessage($this->file_name.$i, $j);
            }
        }
        foreach (['commercial_name', 'scientefic_name', 'provider'] as $i) {
            foreach (['alpha_dash', 'max'] as $j) {
                $ar["$i.$j"] = $this->translateErrorMessage($this->file_name.$i, $j.($j == 'max' ? '.numeric' : ''));
            }
        }
        foreach (['selling_price', 'purchase_price', 'concentrate', 'bonus'] as $i) {
            foreach (['regex'] as $j) {
                $ar["$i.$j"] = $this->translateErrorMessage($this->file_name.$i, $j);
            }
        }

        foreach (['entry_date', 'expire_date'] as $i) {
            foreach (['date', 'after_or_equal'] as $j) {
                $ar["$i.$j"] = $this->translateErrorMessage($this->file_name.$i, $j);
            }
        }

        return $ar;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
