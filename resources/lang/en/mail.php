<?php

return [
    'registration' => [
        'subject' => 'Completing Your Registration – Activate Your RealtyWalk Account',
        'lines' => 'Hello :name,<br/>Thank you for registering with us!<br/>To complete your registration and activate your account, please click the link below:',
        'action_text' => 'Activate Your Account',
        'outro_lines' => [
            '<small>You may copy/paste this link into your brower:</small><br/><small><a href=":activation_link">:activation_link</a></small><br/>',
            'If you did not register for an account, please ignore this email.<br/>If you have any questions, feel free to reach out to our support team.'
        ]
    ],
    'activation' => [
        'subject' => 'Your RealtyWalk Account is Now Active!',
        'lines_with_password' => [
            "Hello :name",
            "Congratulations! Your account has been successfully activated.",
            "You can now log in using the following methods:",
            '<ol><li><strong>Magic Login</strong>: by using your email (:email) <a href=":magic_login_url">on this page</a> (<a href=":magic_login_url">:magic_login_url</a>).</li><li><strong>Email and Password</strong>: you can also log in using your credentials:<br/>Email: <strong>:email</strong><br/>Password: <strong>:password</strong><br/><a href=":normal_login_url">on this page</a> (<a href=":normal_login_url">:normal_login_url</a>).</li></ol>',
            'If you have any questions or need further assistance, feel free to reach out to our support team.'
        ],
        'lines_without_password' => [
            "Hello :name",
            "Congratulations! Your account has been successfully activated.",
            "You can now log in using the following methods:",
            '<ol><li><strong>Magic Login</strong>: by using your email (:email) <a href=":magic_login_url">on this page</a> (<a href=":magic_login_url">:magic_login_url</a>).</li><li><strong>Email and Password</strong>: you can also log in using your credentials <a href=":normal_login_url">on this page</a> (<a href=":normal_login_url">:normal_login_url</a>). If you\'ve forgotten your password, you can reset it using the "Reset Password" link on the login page.</li></ol>',
            'If you have any questions or need further assistance, feel free to reach out to our support team.'
        ]
    ],
    'bug_report' => [
        'greeting' => 'Bug Report',
        'subject' => 'Bug Report',
        'lines' => [
            'A user reporting a bug on the RealtyWalk site with the details below:<br/>',
            'Name: :name<br/>',
            'Email: :email<br/>',
            'URL: :url<br/>',
            'Bug information:<br/>',
            ':body'
        ]
    ],
    'magic_link' => [
        'subject'  => 'Magic sign-in Link for RealtyWalk site',
        'greeting' => 'Hello!',
        'action_text' => 'Sign in to RealtyWalk',
        'lines'    => [
            'You asked us to send you a magic link for quickly signing in to RealtyWalk site. Your wish is our command!'
        ],
        'outro_lines' => [
            '<small>You may copy/paste this link into your brower:</small><br/><small><a href=":magic_link">:magic_link</a><small>',
            'Note: Your magic link will expire in 24 hours, and can only be used one time.'
        ]
    ],
    'daily_report_update' => [
        'greeting' => 'Daily Report Update',
        'subject' => 'Daily Report Update',
        'lines' => [
            'Below is the details of daily updates from <strong>:date</strong><br/>',
            'Added builders: <strong>:total_added_builders</strong><br/>',
            'Updated builders: <strong>:total_updated_builders</strong><br/>',
            'Encountered unmatched builders (from import): <strong>:total_unmatched_builders</strong><br/>',
            'Encountered blacklisted builders (from import): <strong>:total_blacklisted_builders</strong><br/>',
            'Added properties: <strong>:total_added_properties</strong><br/>',
            'Updated properties: <strong>:total_updated_properties</strong><br/>',
            'New registered users: <strong>:total_users</strong><br/>',
            'New favorited properties by users: <strong>:total_favorites</strong><br/>',
            'New added viewing schedules: <strong>:total_schedules</strong><br/>',
            'The reports from data above are attached in this mail message.'
        ]
    ],
    'property_schedule' => [
        'greeting' => 'Viewing Request',
        'subject' => 'Viewing Request from :user on :address',
        'lines' => [
            '<p>:name has scheduled a viewing on your property listing.</p>',
            '<p>Find the details below:</p>',
            '<h5>Property address: :address</h5>',
            '<h5>Property page: <a href=":page_url">:page_url</a></h5>',
            '<h5>Property status: :status</h5>',
            '<h5>Name: :name</h5>',
            '<h5>Email: :email</h5>',
            '<h5>Phone: :phone</h5>',
            '<h5>Schedule Date: :date</h5>',
            '<h5>Schedule Time: :time</h5>',
            '<h5>Extra Notes: </h5>',
            '<p>:message</p>'
        ]
    ]
];
