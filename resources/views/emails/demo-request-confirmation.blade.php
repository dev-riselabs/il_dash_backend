<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Request Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #ec4899;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #1a1a1a;
            margin: 0 0 10px 0;
            font-size: 28px;
        }

        .brand {
            display: inline-block;
            background-color: #ec4899;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #555;
        }

        .section {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .section-title {
            font-weight: bold;
            color: #ec4899;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #efefef;
        }

        .detail-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
            min-width: 150px;
        }

        .detail-value {
            color: #333;
            text-align: right;
            flex: 1;
            padding-left: 10px;
        }

        .objectives-list, .timeline-list {
            list-style: none;
            padding-left: 0;
        }

        .objectives-list li, .timeline-list li {
            padding: 5px 0 5px 20px;
            position: relative;
            color: #555;
        }

        .objectives-list li:before, .timeline-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #ec4899;
            font-weight: bold;
        }

        .notes-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .notes-box p {
            margin: 0;
            color: #555;
            font-style: italic;
        }

        .cta-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f3f4f6;
            border-radius: 6px;
        }

        .cta-button {
            display: inline-block;
            background-color: #ec4899;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin: 10px 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #999;
            font-size: 12px;
        }

        .footer a {
            color: #ec4899;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .detail-row {
                flex-direction: column;
            }

            .detail-value {
                text-align: left;
                margin-top: 5px;
            }

            .cta-button {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="brand">EventsIntel™</div>
            <h1>Demo Request Confirmation</h1>
        </div>

        <!-- Greeting -->
        <p class="greeting">
            Hi {{ $demoRequest->full_name }},<br>
            Thank you for requesting a demo of EventsIntel™! We're excited to showcase how our platform can help you manage and gain insights from your events.
        </p>

        <!-- Section A: Basic Details -->
        <div class="section">
            <div class="section-title">📋 Your Contact Information</div>
            <div class="detail-row">
                <span class="detail-label">Full Name:</span>
                <span class="detail-value">{{ $demoRequest->full_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $demoRequest->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Organization:</span>
                <span class="detail-value">{{ $demoRequest->organization }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Job Title:</span>
                <span class="detail-value">{{ $demoRequest->job_title }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone Number:</span>
                <span class="detail-value">{{ $demoRequest->phone_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Country:</span>
                <span class="detail-value">{{ $demoRequest->country }}</span>
            </div>
        </div>

        <!-- Section B: Event Details -->
        <div class="section">
            <div class="section-title">🎯 Event Information</div>
            <div class="detail-row">
                <span class="detail-label">Event Type:</span>
                <span class="detail-value">{{ $demoRequest->event_type }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Event Name:</span>
                <span class="detail-value">{{ $demoRequest->event_name ?: 'Not specified' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Event Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($demoRequest->event_date)->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Event Location:</span>
                <span class="detail-value">{{ $demoRequest->event_location }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Estimated Attendees:</span>
                <span class="detail-value">{{ $demoRequest->estimated_attendees }}</span>
            </div>
        </div>

        <!-- Section C: Needs & Intent -->
        @if($demoRequest->primary_objectives || $demoRequest->deployment_timeline)
        <div class="section">
            <div class="section-title">🎯 Your Objectives & Timeline</div>
            @if($demoRequest->primary_objectives)
            <div>
                <p style="font-weight: 600; color: #666; margin-bottom: 10px;">What you're looking to achieve:</p>
                <ul class="objectives-list">
                    @foreach($demoRequest->primary_objectives as $objective)
                    <li>{{ $objective }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            @if($demoRequest->deployment_timeline)
            <div style="margin-top: 15px;">
                <p style="font-weight: 600; color: #666; margin-bottom: 10px;">Deployment timeline:</p>
                <ul class="timeline-list">
                    @foreach($demoRequest->deployment_timeline as $timeline)
                    <li>{{ $timeline }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        @endif

        <!-- Section D: Budget -->
        @if($demoRequest->budget_range)
        <div class="section">
            <div class="section-title">💰 Budget Information</div>
            <div class="detail-row">
                <span class="detail-label">Budget Range:</span>
                <span class="detail-value">{{ $demoRequest->budget_range }}</span>
            </div>
        </div>
        @endif

        <!-- Section E: Additional Notes -->
        @if($demoRequest->additional_notes)
        <div class="notes-box">
            <p><strong>Additional Information:</strong></p>
            <p>{!! nl2br(e($demoRequest->additional_notes)) !!}</p>
        </div>
        @endif

        <!-- CTA Section -->
        <div class="cta-section">
            <p style="margin-top: 0; color: #555;">
                <strong>What's Next?</strong><br>
                Our team will review your request and reach out to schedule your personalized demo within 24-48 hours.
            </p>
            <a href="https://eventsintel.com" class="cta-button">Visit EventsIntel</a>
        </div>

        <!-- Additional Info -->
        <p style="color: #666; line-height: 1.8; margin: 20px 0;">
            <strong>Quick Links:</strong><br>
            • <a href="https://eventsintel.com/features" style="color: #ec4899; text-decoration: none;">Explore Features</a><br>
            • <a href="https://eventsintel.com/pricing" style="color: #ec4899; text-decoration: none;">View Pricing</a><br>
            • <a href="https://eventsintel.com/contact" style="color: #ec4899; text-decoration: none;">Contact Sales</a>
        </p>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                © {{ date('Y') }} Rise Networks. All rights reserved.<br>
                <a href="https://eventsintel.com/privacy">Privacy Policy</a> | 
                <a href="https://eventsintel.com/terms">Terms of Service</a>
            </p>
            <p style="margin: 0; color: #bbb;">
                EventsIntel™ | Real-time Event Intelligence Platform<br>
                <a href="mailto:support@risenetworks.org" style="color: #ec4899; text-decoration: none;">support@risenetworks.org</a>
            </p>
        </div>
    </div>
</body>
</html>
