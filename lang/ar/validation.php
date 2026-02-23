<?php

return [

    'accepted' => 'حقل :attribute يجب الموافقة عليه.',
    'accepted_if' => 'حقل :attribute يجب الموافقة عليه عندما يكون :other يساوي :value.',
    'active_url' => 'حقل :attribute يجب أن يكون رابط صحيح.',
    'after' => 'حقل :attribute يجب أن يكون تاريخ بعد :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخ بعد أو يساوي :date.',
    'alpha' => 'حقل :attribute يجب أن يحتوي على أحرف فقط.',
    'alpha_dash' => 'حقل :attribute يجب أن يحتوي على أحرف وأرقام وشرطات فقط.',
    'alpha_num' => 'حقل :attribute يجب أن يحتوي على أحرف وأرقام فقط.',
    'any_of' => 'حقل :attribute غير صالح.',
    'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
    'ascii' => 'حقل :attribute يجب أن يحتوي على أحرف وأرقام ورموز إنجليزية فقط.',
    'before' => 'حقل :attribute يجب أن يكون تاريخ قبل :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخ قبل أو يساوي :date.',

    'between' => [
        'array' => 'حقل :attribute يجب أن يحتوي بين :min و :max عناصر.',
        'file' => 'حجم :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون بين :min و :max.',
        'string' => 'حقل :attribute يجب أن يكون بين :min و :max أحرف.',
    ],

    'boolean' => 'حقل :attribute يجب أن يكون true أو false.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute يجب أن يكون تاريخ صحيح.',
    'date_equals' => 'حقل :attribute يجب أن يكون تاريخ يساوي :date.',
    'date_format' => 'حقل :attribute يجب أن يطابق التنسيق :format.',
    'decimal' => 'حقل :attribute يجب أن يحتوي على :decimal منازل عشرية.',
    'different' => 'حقل :attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'حقل :attribute يجب أن يكون :digits أرقام.',
    'digits_between' => 'حقل :attribute يجب أن يكون بين :min و :max أرقام.',
    'dimensions' => 'أبعاد الصورة في :attribute غير صحيحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'حقل :attribute يجب أن يكون بريد إلكتروني صحيح.',
    'exists' => 'القيمة المختارة لـ :attribute غير صحيحة.',
    'file' => 'حقل :attribute يجب أن يكون ملف.',
    'filled' => 'حقل :attribute مطلوب.',
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'in' => 'القيمة المختارة لـ :attribute غير صحيحة.',
    'integer' => 'حقل :attribute يجب أن يكون عدد صحيح.',
    'ip' => 'حقل :attribute يجب أن يكون عنوان IP صحيح.',
    'ipv4' => 'حقل :attribute يجب أن يكون IPv4 صحيح.',
    'ipv6' => 'حقل :attribute يجب أن يكون IPv6 صحيح.',
    'json' => 'حقل :attribute يجب أن يكون JSON صحيح.',
    'max' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :max عناصر.',
        'file' => 'حجم :attribute يجب ألا يتجاوز :max كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب ألا تكون أكبر من :max.',
        'string' => 'حقل :attribute يجب ألا يزيد عن :max أحرف.',
    ],

    'mimes' => 'حقل :attribute يجب أن يكون ملف من النوع: :values.',
    'mimetypes' => 'حقل :attribute يجب أن يكون ملف من النوع: :values.',

    'min' => [
        'array' => 'حقل :attribute يجب أن يحتوي على الأقل :min عناصر.',
        'file' => 'حجم :attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون على الأقل :min.',
        'string' => 'حقل :attribute يجب أن يكون على الأقل :min أحرف.',
    ],

    'not_in' => 'القيمة المختارة لـ :attribute غير صحيحة.',
    'numeric' => 'حقل :attribute يجب أن يكون رقم.',
    'regex' => 'تنسيق :attribute غير صحيح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'required_with' => 'حقل :attribute مطلوب عند وجود :values.',
    'required_without' => 'حقل :attribute مطلوب عند عدم وجود :values.',
    'same' => 'حقل :attribute يجب أن يطابق :other.',

    'size' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :size عناصر.',
        'file' => 'حجم :attribute يجب أن يكون :size كيلوبايت.',
        'numeric' => 'قيمة :attribute يجب أن تكون :size.',
        'string' => 'حقل :attribute يجب أن يكون :size أحرف.',
    ],

    'string' => 'حقل :attribute يجب أن يكون نص.',
    'timezone' => 'حقل :attribute يجب أن يكون نطاق زمني صحيح.',
    'unique' => 'قيمة :attribute مستخدمة من قبل.',
    'uploaded' => 'فشل رفع :attribute.',
    'url' => 'حقل :attribute يجب أن يكون رابط صحيح.',
    'uuid' => 'حقل :attribute يجب أن يكون UUID صحيح.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    'attributes' => [
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'name' => 'الاسم',
        'token' => 'التوكن',
    ],

];