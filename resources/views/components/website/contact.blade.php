<?php

use App\Models\dashboard\Contact;
use Livewire\Component;

new class extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $subject = '';
    public $message = '';

    // هذه القواعد مطابقة لحقول جدول contacts، والهاتف فقط هو الحقل الاختياري.
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    protected function validationAttributes()
    {
        return [
            'name' => __('faqs.contact_name'),
            'email' => __('faqs.contact_email'),
            'phone' => __('faqs.contact_phone'),
            'subject' => __('faqs.contact_subject'),
            'message' => __('faqs.contact_message'),
        ];
    }

    // wire:model.blur يحدث الخاصية عند مغادرة الحقل، ثم نتحقق من هذا الحقل فقط.
    public function updated(string $property)
    {
        $this->validateOnly($property);
    }

    public function send()
    {
        $validated = $this->validate();

        Contact::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        // بعد الحفظ نفرغ الحقول ونزيل رسائل التحقق القديمة.
        $this->reset('name', 'email', 'phone', 'subject', 'message');
        $this->resetValidation();

        session()->flash('contact_success', __('faqs.contact_success'));
    }
};
?>

<section class="faq-contact-section">
    <style>
        .faq-contact-section {
            background: #fffafe;
            padding: 6rem 0;
        }

        .faq-contact-wrapper {
            display: grid;
            gap: 4rem;
            grid-template-columns: minmax(0, 0.8fr) minmax(0, 1.2fr);
            margin: 0 auto;
            max-width: 110rem;
        }

        .faq-contact-copy {
            align-self: center;
        }

        .faq-contact-copy h2 {
            font-size: 3.2rem;
            margin-bottom: 1.4rem;
        }

        .faq-contact-copy p {
            font-size: 1.6rem;
            line-height: 1.9;
            max-width: 42rem;
        }

        .faq-contact-form {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 6px;
            padding: 3rem;
        }

        .faq-contact-fields {
            display: grid;
            gap: 2rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .faq-contact-field {
            min-width: 0;
        }

        .faq-contact-field-full {
            grid-column: 1 / -1;
        }

        .faq-contact-field label {
            color: #232532;
            display: block;
            font-family: inter, sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
        }

        .faq-contact-field input,
        .faq-contact-field textarea {
            background: #fff;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            color: #232532;
            font-family: inter, sans-serif;
            font-size: 1.5rem;
            outline: none;
            padding: 1.3rem 1.5rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
        }

        .faq-contact-field input:focus,
        .faq-contact-field textarea:focus {
            border-color: #ae1c9a;
            box-shadow: 0 0 0 3px rgba(174, 28, 154, 0.12);
        }

        .faq-contact-field textarea {
            min-height: 15rem;
            resize: vertical;
        }

        .faq-contact-field .is-invalid {
            border-color: #dc3545;
        }

        .faq-contact-error {
            color: #dc3545;
            display: block;
            font-family: inter, sans-serif;
            font-size: 1.3rem;
            margin-top: 0.6rem;
        }

        .faq-contact-success {
            background: #eaf7ee;
            border: 1px solid #b7dfc2;
            border-radius: 5px;
            color: #246b37;
            font-family: inter, sans-serif;
            font-size: 1.4rem;
            margin-bottom: 2rem;
            padding: 1.2rem 1.5rem;
        }

        .faq-contact-submit {
            background: #ae1c9a;
            border: 1px solid #ae1c9a;
            border-radius: 5px;
            color: #fff;
            font-family: inter, sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            min-height: 4.8rem;
            padding: 1.2rem 2.6rem;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .faq-contact-submit:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }

        .faq-contact-submit:hover,
        .faq-contact-submit:focus {
            background: #232532;
            border-color: #232532;
            color: #fff;
        }

        @media (max-width: 991.98px) {
            .faq-contact-wrapper {
                gap: 2.5rem;
                grid-template-columns: 1fr;
            }

            .faq-contact-copy p {
                max-width: none;
            }
        }

        @media (max-width: 575.98px) {
            .faq-contact-section {
                padding: 3.5rem 0;
            }

            .faq-contact-copy h2 {
                font-size: 2.6rem;
            }

            .faq-contact-form {
                padding: 2rem;
            }

            .faq-contact-fields {
                grid-template-columns: 1fr;
            }

            .faq-contact-field-full {
                grid-column: auto;
            }

            .faq-contact-submit {
                width: 100%;
            }
        }
    </style>

    <div class="container">
        <div class="faq-contact-wrapper">
            <div class="faq-contact-copy">
                <h2>{{ __('faqs.contact_title') }}</h2>
                <p>{{ __('faqs.contact_intro') }}</p>
            </div>

            <form wire:submit="send" class="faq-contact-form">
                @if (session()->has('contact_success'))
                    <div class="faq-contact-success" role="alert">
                        {{ session('contact_success') }}
                    </div>
                @endif

                <div class="faq-contact-fields">
                    <div class="faq-contact-field">
                        <label for="contact_name">{{ __('faqs.contact_name') }}</label>
                        <input
                            type="text"
                            id="contact_name"
                            wire:model.blur="name"
                            @error('name') class="is-invalid" @enderror
                            placeholder="{{ __('faqs.contact_name_placeholder') }}"
                            autocomplete="name"
                        >
                        @error('name')
                            <span class="faq-contact-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="faq-contact-field">
                        <label for="contact_email">{{ __('faqs.contact_email') }}</label>
                        <input
                            id="contact_email"
                            wire:model.blur="email"
                            @error('email') class="is-invalid" @enderror
                            placeholder="{{ __('faqs.contact_email_placeholder') }}"
                            autocomplete="email"
                            dir="ltr"
                        >
                        @error('email')
                            <span class="faq-contact-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="faq-contact-field">
                        <label for="contact_phone">{{ __('faqs.contact_phone') }}</label>
                        <input
                            type="tel"
                            id="contact_phone"
                            wire:model.blur="phone"
                            @error('phone') class="is-invalid" @enderror
                            placeholder="{{ __('faqs.contact_phone_placeholder') }}"
                            autocomplete="tel"
                            dir="ltr"
                        >
                        @error('phone')
                            <span class="faq-contact-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="faq-contact-field">
                        <label for="contact_subject">{{ __('faqs.contact_subject') }}</label>
                        <input
                            type="text"
                            id="contact_subject"
                            wire:model.blur="subject"
                            @error('subject') class="is-invalid" @enderror
                            placeholder="{{ __('faqs.contact_subject_placeholder') }}"
                        >
                        @error('subject')
                            <span class="faq-contact-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="faq-contact-field faq-contact-field-full">
                        <label for="contact_message">{{ __('faqs.contact_message') }}</label>
                        <textarea
                            id="contact_message"
                            wire:model.blur="message"
                            @error('message') class="is-invalid" @enderror
                            placeholder="{{ __('faqs.contact_message_placeholder') }}"
                        ></textarea>
                        @error('message')
                            <span class="faq-contact-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button
                    type="submit"
                    class="faq-contact-submit"
                    wire:loading.attr="disabled"
                    wire:target="send"
                >
                    <span wire:loading.remove wire:target="send">
                        {{ __('faqs.contact_send') }}
                    </span>
                    <span wire:loading wire:target="send">
                        {{ __('faqs.contact_sending') }}
                    </span>
                </button>
            </form>
        </div>
    </div>
</section>
