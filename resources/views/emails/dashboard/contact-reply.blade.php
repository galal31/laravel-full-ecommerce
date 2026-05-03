<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $replySubject }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">

    <div style="max-width: 650px; margin: auto; background: #ffffff; padding: 25px; border-radius: 8px;">

        <h2 style="margin-top: 0; color: #333;">
            {{ $replySubject }}
        </h2>

        <p style="color: #555; line-height: 1.7;">
            Hello {{ $contact->name }},
        </p>

        <div style="color: #333; line-height: 1.8; white-space: pre-line;">
            {{ $replyMessage }}
        </div>

        <hr style="margin: 25px 0; border: none; border-top: 1px solid #ddd;">

        <p style="font-size: 13px; color: #777;">
            This is a reply to your message:
        </p>

        <p style="font-size: 14px; color: #555;">
            <strong>Subject:</strong> {{ $contact->subject }}
        </p>

        <p style="font-size: 14px; color: #555;">
            <strong>Your message:</strong><br>
            {{ $contact->message }}
        </p>

    </div>

</body>
</html>