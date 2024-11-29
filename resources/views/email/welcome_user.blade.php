<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{config('app.name')}}</title>
</head>
<body>
    <table style="width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <tr>
            <td style="text-align: center;">
                <h2>Welcome to {{config('app.name')}}</h2>
            </td>
        </tr>
        <tr>
            <td>
                <p>Hello {{ $user->name }},</p>
                <p>Thank you for signing up on {{config('app.name')}}. We are thrilled to have you on board!</p>
                <p>Your Login Credentials are given below:</p>
                <p>Email: {{$user->email}}</p>
                <p>Password: {{$user->show_password}}</p>
            </td>
        </tr>
    </table>
</body>
</html>
