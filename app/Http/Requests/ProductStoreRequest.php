<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:products,name',
                'regex:/^[a-zA-Z0-9\s\-_.,!?]+$/',
                'not_regex:/<script|javascript:|vbscript:|on\w+\s*=/i'
            ],
            'description' => [
                'required',
                'string',
                'min:10',
                'max:5000',
                'not_regex:/<script|javascript:|vbscript:|on\w+\s*=/i'
            ],
            'marketing_description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
                'not_regex:/<script|javascript:|vbscript:|on\w+\s*=/i'
            ],
            'price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'discount_price' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:9999999999999.99',
                'lt:price',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'category' => [
                'required',
                'string',
                'max:255',
                'in:earring,bracelet,necklace,ring'
            ],
            'features' => [
                'nullable',
                'array',
                'max:10'
            ],
            'features.*' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_.,!?]+$/',
                'not_regex:/<script|javascript:|vbscript:|on\w+\s*=/i'
            ],
            'customer_images' => [
                'required',
                'array',
                'min:1',
                'max:5'
            ],
            'customer_images.*' => [
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:2048',
                'mimetypes:image/jpeg,image/png,image/jpg,image/gif,image/webp'
            ],
            'product_video' => [
                'nullable',
                'file',
                'mimes:mp4,avi,mov',
                'max:10240',
                'mimetypes:video/mp4,video/avi,video/quicktime'
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => $this->sanitizeInput($this->name ?? ''),
            'description' => $this->sanitizeInput($this->description ?? ''),
            'marketing_description' => $this->sanitizeInput($this->marketing_description ?? ''),
        ]);

        // Sanitize features array
        if ($this->has('features') && is_array($this->features)) {
            $sanitizedFeatures = array_map([$this, 'sanitizeInput'], $this->features);
            $this->merge(['features' => $sanitizedFeatures]);
        }
    }

    /**
     * Sanitize input to prevent XSS and injection attacks.
     */
    private function sanitizeInput($input)
    {
        if (is_string($input)) {
            // 1. 移除前后空白字符
            $input = trim($input);
            
            // 2. 移除所有HTML标签
            $input = strip_tags($input);
            
            // 3. 转义特殊字符防止XSS
            $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8', true);
            
            // 4. 移除潜在的脚本标签和事件处理器
            $input = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $input);
            $input = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $input);
            
            // 5. 移除JavaScript协议
            $input = preg_replace('/javascript:/i', '', $input);
            $input = preg_replace('/vbscript:/i', '', $input);
            $input = preg_replace('/data:/i', '', $input);
            
            // 6. 移除SQL注入常见字符
            $input = str_replace(['--', '/*', '*/', 'xp_', 'sp_'], '', $input);
            
            // 7. 限制长度防止缓冲区溢出
            $input = mb_substr($input, 0, 10000, 'UTF-8');
            
            return $input;
        }
        return $input;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name must not exceed 255 characters.',
            'name.unique' => 'This product name already exists. Please choose a different name.',
            'name.regex' => 'Product name contains invalid characters. Only letters, numbers, spaces, hyphens, underscores, periods, commas, exclamation marks, and question marks are allowed.',
            'description.required' => 'Product description is required.',
            'description.min' => 'Product description must be at least 10 characters.',
            'description.max' => 'Product description must not exceed 5000 characters.',
            'marketing_description.required' => 'Marketing description is required.',
            'marketing_description.min' => 'Marketing description must be at least 10 characters.',
            'marketing_description.max' => 'Marketing description must not exceed 2000 characters.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be at least 0.01.',
            'price.regex' => 'Price must be a valid decimal number with up to 2 decimal places.',
            'discount_price.numeric' => 'Discount price must be a valid number.',
            'discount_price.min' => 'Discount price must be at least 0.01.',
            'discount_price.lt' => 'Discount price must be less than regular price.',
            'discount_price.regex' => 'Discount price must be a valid decimal number with up to 2 decimal places.',
            'category.required' => 'Product category is required.',
            'category.in' => 'Category must be one of: earring, bracelet, necklace, ring.',
            'features.array' => 'Features must be an array.',
            'features.max' => 'Maximum 10 features allowed.',
            'features.*.required' => 'Each feature is required.',
            'features.*.string' => 'Each feature must be a string.',
            'features.*.max' => 'Each feature must not exceed 255 characters.',
            'features.*.regex' => 'Each feature contains invalid characters. Only letters, numbers, spaces, hyphens, underscores, periods, commas, exclamation marks, and question marks are allowed.',
            'customer_images.array' => 'Images must be an array.',
            'customer_images.max' => 'Maximum 5 images allowed.',
            'customer_images.*.image' => 'Each file must be an image.',
            'customer_images.*.mimes' => 'Images must be in JPEG, PNG, JPG, GIF, or WebP format.',
            'customer_images.*.max' => 'Each image must not exceed 2MB.',
            'product_video.file' => 'Video must be a valid file.',
            'product_video.mimes' => 'Video must be in MP4, AVI, or MOV format.',
            'product_video.max' => 'Video must not exceed 10MB.',
        ];
    }
}
