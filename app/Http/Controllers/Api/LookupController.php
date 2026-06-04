<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDay;
use App\Models\EventSession;
use App\Models\Sector;
use App\Models\Track;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    public function events(): JsonResponse
    {
        return response()->json(Event::orderBy('name')->get(['id', 'name', 'slug', 'description', 'starts_at', 'ends_at', 'status']));
    }

    public function eventDays(): JsonResponse
    {
        return response()->json(EventDay::orderBy('date')->get(['id', 'event_id', 'day_no', 'date', 'label']));
    }

    public function tracks(): JsonResponse
    {
        return response()->json(Track::orderBy('name')->get(['id', 'name', 'slug', 'color']));
    }

    public function sectors(): JsonResponse
    {
        return response()->json(Sector::orderBy('name')->get(['id', 'name', 'slug', 'color']));
    }

    public function venues(): JsonResponse
    {
        return response()->json(Venue::orderBy('name')->get(['id', 'name', 'slug', 'capacity', 'status']));
    }

    public function sessionOptions(): JsonResponse
    {
        return response()->json(EventSession::orderBy('starts_at')->get(['id', 'title', 'starts_at']));
    }

    public function owners(): JsonResponse
    {
        return response()->json(User::orderBy('name')->get(['id', 'name', 'email']));
    }

    public function countries(): JsonResponse
    {
        $countries = [
            'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
            'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi',
            'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic',
            'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic',
            'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia',
            'Fiji', 'Finland', 'France',
            'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
            'Haiti', 'Honduras', 'Hungary',
            'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy',
            'Jamaica', 'Japan', 'Jordan',
            'Kazakhstan', 'Kenya', 'Kiribati', 'Kosovo', 'Kuwait', 'Kyrgyzstan',
            'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
            'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar',
            'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway',
            'Oman',
            'Pakistan', 'Palau', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal',
            'Qatar',
            'Romania', 'Russia', 'Rwanda',
            'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain', 'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria',
            'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu',
            'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan',
            'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam',
            'Yemen',
            'Zambia', 'Zimbabwe'
        ];
        
        return response()->json(array_map(fn($c) => ['id' => $c, 'name' => $c], $countries));
    }

    public function jobTitles(): JsonResponse
    {
        $jobTitles = [
            'CEO', 'Chief Executive Officer', 'CTO', 'Chief Technology Officer', 'CFO', 'Chief Financial Officer', 'COO', 'Chief Operating Officer',
            'President', 'Vice President', 'Executive Director', 'Managing Director', 'Head of Department',
            'Founder', 'Co-founder', 'Co-founder & CEO',
            'Director', 'Senior Director', 'Associate Director',
            'Manager', 'Senior Manager', 'Project Manager', 'Product Manager', 'Program Manager', 'Department Manager',
            'Engineer', 'Senior Engineer', 'Lead Engineer', 'Principal Engineer', 'Software Engineer', 'Data Engineer', 'Systems Engineer',
            'Administrator', 'System Administrator', 'Network Administrator', 'Database Administrator',
            'Analyst', 'Senior Analyst', 'Business Analyst', 'Data Analyst', 'Financial Analyst',
            'Developer', 'Senior Developer', 'Full Stack Developer', 'Front End Developer', 'Back End Developer', 'Mobile Developer',
            'Designer', 'UX Designer', 'UI Designer', 'Graphic Designer', 'Product Designer',
            'Architect', 'Solutions Architect', 'Enterprise Architect', 'Data Architect',
            'Consultant', 'Senior Consultant', 'Management Consultant', 'Business Consultant',
            'Specialist', 'Senior Specialist', 'Technical Specialist',
            'Officer', 'Head of', 'Chief', 'VP',
            'HOO', 'HOD', 'Head of Operations', 'Head of Technology', 'Head of Sales', 'Head of Marketing', 'Head of HR',
            'Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.', 'Instructor', 'Professor', 'Associate Professor', 'Assistant Professor',
            'Speaker', 'Panelist', 'Moderator', 'Keynote Speaker',
        ];
        
        sort($jobTitles);
        return response()->json(array_map(fn($t) => ['id' => $t, 'name' => $t], $jobTitles));
    }
}
