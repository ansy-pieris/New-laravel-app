<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);
        
        // Here you would typically send an email or save to database
        // Mail::to('admin@example.com')->send(new ContactMessage($request->all()));
        
        return redirect()->route('contact')->with('success', 'Your message has been sent successfully!');
    }
}