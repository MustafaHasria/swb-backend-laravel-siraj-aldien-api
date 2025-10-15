<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    /**
     * Submit contact form
     * POST /api/contact-us
     */
    public function submit(Request $request): JsonResponse
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'sender_name' => 'required|string|max:255',
                'sender_email' => 'required|email|max:255',
                'sender_phone' => 'required|string|max:20',
                'sender_country' => 'required|string|max:255',
                'sender_message' => 'required|string|max:2000',
                'captcha_code' => 'nullable|string|max:10'
            ], [
                'sender_name.required' => 'Sender name is required',
                'sender_email.required' => 'Email address is required',
                'sender_email.email' => 'Invalid email format',
                'sender_phone.required' => 'Phone number is required',
                'sender_country.required' => 'Country is required',
                'sender_message.required' => 'Message is required',
                'sender_message.max' => 'Message must be less than 2000 characters'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the following errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Save the contact form data to the database
            $contact = ContactUs::create([
                'contact_us_sender_name' => $request->input('sender_name'),
                'contact_us_sender_email' => $request->input('sender_email'),
                'contact_us_sender_phone' => $request->input('sender_phone'),
                'contact_us_sender_country' => $request->input('sender_country'),
                'contact_us_sender_message' => $request->input('sender_message'),
                'contact_us_date' => now(),
                'contact_us_lan' => app()->getLocale() ?? 'ar',
                'contact_us_active' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully! We will contact you soon.',
                'data' => [
                    'contact_id' => $contact->contact_us_id,
                    'submission_date' => $contact->contact_us_date
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your message. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
